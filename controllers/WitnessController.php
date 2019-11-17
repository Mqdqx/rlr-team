<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\Wish;
use app\models\Community;

class WitnessController extends Controller
{
    /**
     * 指定当前控制器的模板
     */
    public $layout = 'witness';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            //无权限访问过滤且报错
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','finance','wish','user','team','generate','personalinfo'],
                'rules' => [
                    [
                        'actions' => ['index','finance','wish','user','team','generate','personalinfo'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule,$action) {
                            $_role = Yii::$app->user->identity->role;
                            return ($_role !== 'witness') ? ($this->redirect(['site/error'])) : true;
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
     * 见证人个人信息(所绑定社区)
     */
    public function actionPersonalinfo()
    {
        if (Yii::$app->request->get('option') == '') {
            $community = Community::findOne(['user_id'=>Yii::$app->user->identity->user_id]);
            if (!$community) {
                $community = new Community();
                $community->scenario = 'newone';
                if (Yii::$app->request->isPost) {
                    $post = Yii::$app->request->post();
                    if ($community->newone($post)) {
                        Yii::$app->session->setFlash('submit');
                        return $this->redirect(['witness/personalinfo']);
                    }
                }
            }
        }elseif (Yii::$app->request->get('option') == 'modify') {
            
        }
        return $this->render('personalinfo',['community'=>$community]);
    }

    /**
     * 社区流水功能
     */
    public function actionFinance()
    {
        $data = '社区流水';
        return $this->render('finance',['data'=>$data]);
    }

    /**
     * 见证人的 本社区心愿管理功能
     * 未激活心愿和生成心愿码/待审心愿、已审心愿、资助周期中心愿、已完成心愿
     */
    public function actionWish()
    {
        if (Yii::$app->user->identity->status == 3) {
            return $this->render('wish');
        }
        switch (Yii::$app->request->get('option')) {
            //未激活心愿和生成心愿码
            case 'noactivate':
                $models = Wish::find_Witness('noactivate');
                break;
            //待审心愿
            case 'waiting':
                $models = Wish::find_Witness('waiting');
                if (Yii::$app->request->get('wish_id')) {
                    $model = Wish::findOne(['wish_id'=>Yii::$app->request->get('wish_id')]);
                    if (Yii::$app->request->isPost) {
                        $post = Yii::$app->request->post();
                        if ($model->approve($post,Yii::$app->request->get('approve'))) {
                            Yii::$app->session->setFlash('approved',$model->wish_id);
                            return $this->redirect(['witness/wish','option'=>'waiting']);
                        }
                    }
                    return $this->render('wish',['wish'=>$model]);
                }
                break;
            //已审心愿
            case 'approved':
                //推广
                //删除
                $models = Wish::find_Witness('approved');
                break;
            //待启动
            case 'start':
                if (Yii::$app->request->get('wish_id')) {
                    $model = Wish::findOne(['wish_id'=>Yii::$app->request->get('wish_id')]);
                    $model->scenario = 'start';
                    if (Yii::$app->request->isPost) {
                        $post = Yii::$app->request->post();
                        if ($model->start($post)) {
                            $mailer = Yii::$app->mailer->compose('wish_start',['role'=>'vip','name'=>$model->user->truename,'wish_id'=>$model->wish_id]);
                            $mailer->setFrom(Yii::$app->params['senderEmail']);
                            $mailer->setTo($model->user->email);
                            $mailer->setSubject('人恋人平台-心愿启动通知');
                            $mailer->send();
                            $mailer = Yii::$app->mailer->compose('wish_start',['role'=>'sponsor','name'=>$model->getUsername('sponsor'),'wish_id'=>$model->wish_id]);
                            $mailer->setFrom(Yii::$app->params['senderEmail']);
                            $mailer->setTo($model->sponsor->email);
                            $mailer->setSubject('人恋人平台-心愿启动通知');
                            $mailer->send();
                            Yii::$app->session->setFlash('started',$model->wish_id.'启动成功！');
                            return $this->redirect(['witness/wish','option'=>'start']);
                        }
                    } else {
                        $model->_starttime = date('y-m-d H:i:s',time());
                    }
                    return $this->render('wish',['wish'=>$model]);
                }
                $models = Wish::find_Witness('start');
                break;
            //资助周期中
            case 'supporting':
                $models = Wish::find_Witness('supporting');
                break;
            //已完成心愿
            case 'finished':
                $models = Wish::find_Witness('finished');
                break;
            //其它传参抛出异常
            default:
                throw new NotFoundHttpException("警告！越权操作！");
                break;
        }
        $count = $models->count();
        $pageSize = Yii::$app->params['pageSize'];
        $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $models = $models->offset($pager->offset)->limit($pager->limit)->all(); //分页处理
        return $this->render('wish',['models'=>$models,'pager'=>$pager]);
    }

    /**
     * 所管社区旗下用户管理
     * 审核 学生 用户注册
     */
    public function actionUser()
    {
        switch (Yii::$app->request->get('option')) {
            //用户管理
            case 'manage':
                $data = '用户管理';
                break;
            //其它传参抛出异常
            default:
                throw new NotFoundHttpException("警告！越权操作！");
                break;
        }
        return $this->render('user',['data'=>$data]);
    }

    /**
     * 所管社区旗下团体管理
     */
    public function actionTeam()
    {
        $data = '社区管理';
        return $this->render('team',['data'=>$data]);
    }

    /**
     * 产生心愿(心愿码)
     */
    public function actionGenerate()
    {
        if (Yii::$app->user->identity->status !== 1) {
            throw new NotFoundHttpException("警告！越权操作！");
        }
        $model = new Wish();
        if ($model->generateToken()) {
            Yii::$app->session->setFlash('generateWish','新建成功');
        }
        return $this->redirect(['witness/wish','option'=>'noactivate']);
    }
}
