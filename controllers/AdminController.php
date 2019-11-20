<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\User;
use app\models\Vote;
use app\models\Flows;
use app\models\Community;

class AdminController extends Controller
{
    /**
     * 指定当前控制器的模板
     */
    public $layout = 'admin';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            //无权限访问过滤且报错
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','release','finance','community','team','user','wish'],
                'rules' => [
                    [
                        'actions' => ['index','release','finance','community','team','user','wish'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule,$action) {
                            $_role = Yii::$app->user->identity->role;
                            return ($_role !== 'admin') ? ($this->redirect(['site/error'])) : true;
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
     * 首页发布功能
     */
    public function actionRelease()
    {
        $data = '首页发布功能';
        return $this->render('release',['data'=>$data]);
    }

    /**
     * 平台财务功能
     */
    public function actionFinance()
    {
        $balance = (new \yii\db\Query)->select([])->from('user')->sum('balance');
        $balance += (new \yii\db\Query)->select([])->where(['status'=>1])->from('flows')->sum('money');
        $dataProvider = new ActiveDataProvider([
            'query'=>Flows::find()->where(['status'=>1]),
            'pagination' => ['pagesize' => 10],
        ]);

        return $this->render('finance',['balance'=>$balance,'dataProvider'=>$dataProvider]);
    }

    /**
     * 社区管理功能
     */
    public function actionCommunity()
    {
        //判断横向导航栏的选项(get请求)传递到不同的action
        switch (Yii::$app->request->get('option')) {
            //社区管理模块
            case 'manage':
                $dataProvider = new ActiveDataProvider([
                    'query'=> Community::find()->where([]),
                    'pagination' => ['pagesize' => 10],
                ]);
                return $this->render('community',['dataProvider'=>$dataProvider]);
                break;
            //收到一位见证人witness邮寄过来的文件资料，审核一个社区且关联
            case 'approve':
                $community = Community::findOne(['community_id'=>Yii::$app->request->get('community_id')]);
                $community->truename = $community->user->truename;
                $community->number = $community->user->number;
                $community->scenario = 'newone';
                if (Yii::$app->request->isPost) {
                    $post = Yii::$app->request->post();
                    if ($community->approve($post)) {
                        Yii::$app->session->setFlash('approved',$community->community_name);
                        return $this->redirect(['admin/community','option'=>'manage']);
                    }
                }
                return $this->render('community',['community'=>$community]);
                break;
            default:
                throw new NotFoundHttpException("警告！越权操作！");
                break;
        }
    }

    /**
     * 团体管理功能
     */
    public function actionTeam()
    {
        if (Yii::$app->request->get('option')=='cron') {
            $dawnYest = 86400+strtotime(date("Y-m-d"),time());//明天0点0分1秒
            $models = Vote::findAll(['status'=>1]);
            $result = [];
            foreach ($models as $key => $vote) {
                Yii::$app->session->set('team',$vote->team);
                //如果此状态为进行中的投票活动自动结束时间在 明天0-0-0到24-59-59中
                if ($vote->endtime>$dawnYest && $vote->endtime<$dawnYest+86400) {
                    $minBallot = $vote->findMinballot();
                    $noVote = $vote->noComplete();
                    if (count($vote->res)==1) {
                        //如果只有一个候选者心愿则满足统计结果
                        //统计结果 发送邮件
                        $vote->statistics();
                    } elseif ((!$minBallot) && $noVote) {
                        //若存在并行最小 并且还有团体成员没有投票
                        //发送邮件提醒未投票成员参与
                        foreach ($noVote as $k => $user) {
                            $mailer = Yii::$app->mailer->compose('vote',['vote_id'=>$vote->vote_id,'team_name'=>Yii::$app->session['team']->name ,'email'=>$user['email']]);
                            $mailer->setFrom(Yii::$app->params['senderEmail']);
                            $mailer->setTo($user['email']);
                            $mailer->setSubject('人恋人平台-投票活动提醒');
                            $mailer->send();
                        }
                        Yii::$app->session->setFlash('noMinballot');
                    } elseif ((!$minBallot) && (!$noVote)) {
                        //若存在并行最小 并且所有成员都参与了投票
                        //结束时间延长一天 且 清空当前投票结果
                        $vote->reset();
                    } else {
                        //满足统计结果
                        //统计结果 发送邮件
                        $vote->statistics();
                    }
                    //$result[$vote->vote_id]缓存结果
                    $flash = ['voteoneSuccess','voteoneFail','noMinballot','reset','statistics'];
                    foreach ($flash as $key => $value) {
                        if (Yii::$app->session->hasFlash($value)) {$result[$vote->vote_id] = Yii::$app->session->getFlash($value);}
                    }
                    file_put_contents('../log/votelog.txt','|___'.date('YmdHis').'*'.json_encode($result).'___|'.PHP_EOL,FILE_APPEND);//写入日志                
                }
            }
            return $this->render('team',['result'=>$result]);
        }

        return $this->render('team');
    }

    /**
     * 用户管理功能
     */
    public function actionUser()
    {
        //判断横向导航栏的选项(get请求)传递到不同的action
        switch (Yii::$app->request->get('option')) {
            //用户管理
            case 'manage':
                $data = '用户管理';
                return $this->render('user',['data'=>$data]);
                break;
            default:
                throw new NotFoundHttpException("警告！越权操作！");
                break;
        }
    }

    /**
     * 所有心愿管理功能
     */
    public function actionWish()
    {
        //
        $data = '心愿管理';
        return $this->render('wish',['data'=>$data]);
    }
}
