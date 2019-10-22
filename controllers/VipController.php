<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\User;
use app\models\Wish;
use app\models\Flows;

class VipController extends Controller
{
    /**
     * 指定当前控制器的模板
     */
    public $layout = 'vip';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            //无权限访问过滤且报错
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','finance','support','wish','mywish'],
                'rules' => [
                    [
                        'actions' => ['index','finance','support','wish','mywish'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule,$action) {
                            $_role = Yii::$app->user->identity->role;
                            return ($_role !== 'vip') ? ($this->redirect(['site/error'])) : true;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /**
     * 个人钱包流水功能:充值，体现，查询
     */
    public function actionFinance()
    {
        $models = Flows::find()->where(['or',['and','in_id='.Yii::$app->user->identity->user_id,'in_role="vipPurse"'],['and','out_id='.Yii::$app->user->identity->user_id,'out_role="vipPurse"']])->orderBy(['createtime'=>SORT_DESC]);
        $count = $models->count();
        $pageSize = Yii::$app->params['pageSize'];
        $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $models = $models->offset($pager->offset)->limit($pager->limit)->all(); //分页处理
        switch (Yii::$app->request->get('option')) {
            //流水
            case 'flows':

                return $this->render('finance',['models'=>$models,'pager'=>$pager]);
                break;
            //充值
            case 'recharge':
                $model = new Flows();
                $model->scenario = "recharge";
                if (Yii::$app->request->isPost) {
                    $post = Yii::$app->request->post();
                    $trade = $model->rechargeAlipay($post);
                    if (!$trade) {
                        Yii::$app->session->setFlash('rechargeFail');
                    } else {
                        //接入支付宝 SDK
                        require_once './../vendor/alipay/config.php';
                        require_once './../vendor/alipay/pagepay/service/AlipayTradeService.php';
                        require_once './../vendor/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';
                        $out_trade_no = trim($trade->out_trade_no);//商户订单号，商户网站订单系统中唯一订单号，必填
                        $subject = '用户充值';//订单名称，必填
                        $total_amount = trim($trade->money);//付款金额，必填
                        $body = Yii::$app->user->identity->email.'支付宝充值';//商品描述，可空
                        //构造参数
                        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
                        $payRequestBuilder->setBody($body);
                        $payRequestBuilder->setSubject($subject);
                        $payRequestBuilder->setTotalAmount($total_amount);
                        $payRequestBuilder->setOutTradeNo($out_trade_no);
                        //发起支付宝交易
                        $aop = new \AlipayTradeService($config);
                        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);
                        var_dump($response);
                        exit();
                        //接入结束
                    }
                }
                return $this->render('finance',['models'=>$models,'pager'=>$pager,'model'=>$model]);
                break;
            //提现
            case 'withdraw':
                
                return $this->render('finance',['models'=>$models,'pager'=>$pager]);
                break;
            default:
                throw new NotFoundHttpException("警告！越权操作！");
                break;
        }        
    }

    /**
     * 资助者身份：我的资助 查看功能
     */
    public function actionSupport()
    {
        $models = Wish::find()->where(['locking_user_id'=>Yii::$app->user->identity->user_id]);
        $count = $models->count();
        $pageSize = Yii::$app->params['pageSize'];
        $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $models = $models->offset($pager->offset)->limit($pager->limit)->all(); //分页处理
        return $this->render('support',['models'=>$models,'pager'=>$pager]);
    }

    /**
     * 资助者身份：查看对应范围(社区)学生发布且已审核的心愿
     * 操作：资助(绑定) 一个心愿
     */
    public function actionWish()
    {
        if (Yii::$app->request->get('option') == 'support') {
            $wish = Wish::findOne(['wish_id'=>Yii::$app->request->get('wish_id')]);
            if ($wish->minbalance > Yii::$app->user->identity->balance) {
                Yii::$app->session->setFlash('support','您当前余额小于该心愿期望值的最小余额比！无法资助心愿'.$wish->wish_id);
            } elseif ($wish->status !== 2) { // 验证数据是否过期
                Yii::$app->session->setFlash('support','心愿编号：'.$wish->wish_id.'已被其它用户抢先资助了，请重新选择！');
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $wish->locking_user_id = Yii::$app->user->identity->user_id;
                    $wish->locking_time = time();
                    $wish->status = 3;
                    if (!$wish->save()) {
                        throw new \Exception();
                    }
                    $transaction->commit();
                    Yii::$app->session->setFlash('support','心愿：'.$wish->wish_id.'资助成功，请静候相关负责人联系您办理接下来的手续！');
                } catch (\Exception $e) {
                    $transaction->rollback();
                    Yii::$app->session->setFlash('support','心愿：'.$wish->wish_id.'操作失败，可能是网络问题，请稍后刷新重试！');
                }
            }
            return $this->redirect(['vip/wish']);//去掉get传参的wish_id和option
        }
        $models = Wish::find()->where(['and','user_id !='.Yii::$app->user->identity->user_id,'status = 2']);
        $count = $models->count();
        $pageSize = Yii::$app->params['pageSize'];
        $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $models = $models->offset($pager->offset)->limit($pager->limit)->all(); //分页处理
        return $this->render('wish',['models'=>$models,'pager'=>$pager]);
    }

    /**
     * 以被资助者身份 我的心愿 功能：1.查看关于自己的心愿，2.发布一个心愿
     */
    public function actionMywish()
    {
        //由get传参右侧横向导航option的不同渲染不同功能
        switch (Yii::$app->request->get('option')) {
            //查看心愿
            case 'see':
                $models = Wish::findAll(['user_id'=>Yii::$app->user->identity->user_id]);
                return $this->render('mywish',['models'=>$models]);
                break;
            //发布一个心愿    
            case 'newone':
                $model = new Wish();
                if (Yii::$app->request->isPost) {
                    $post = Yii::$app->request->post();
                    if ($model->publish($post)) {
                        Yii::$app->session->setFlash('published');
                        return $this->refresh();
                    }
                }
                return $this->render('mywish',['model'=>$model]);
                break;
            //其它传参报错
            default:
                throw new NotFoundHttpException("警告！越权操作！");
                break;
        }
    }

}
