<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;

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
                'only' => ['index','finance','wish','user','team'],
                'rules' => [
                    [
                        'actions' => ['index','finance','wish','user','team'],
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
     * 社区流水功能
     */
    public function actionFinance()
    {
        $data = '社区流水';
        return $this->render('finance',['data'=>$data]);
    }

    /**
     * 见证人的 本社区心愿管理功能
     * 待审心愿、已审心愿、资助周期中心愿、已完成心愿
     */
    public function actionWish()
    {
        switch (Yii::$app->request->get('option')) {
            //待审心愿
            case 'waiting':
                $data = '待审心愿';
                break;
            //已审心愿
            case 'approved':
                $data = '已审心愿';
                break;
            //资助周期中
            case 'supporting':
                $data = '资助周期中';
                break;
            //已完成心愿
            case 'finished':
                $data = '已完成心愿';
                break;
            //其它传参抛出异常
            default:
                throw new NotFoundHttpException("警告！越权操作！");
                break;
        }
        return $this->render('wish',['data'=>$data]);
    }

    /**
     * 所管社区旗下用户管理
     * 审核 学生 用户注册
     */
    public function actionUser()
    {
        switch (Yii::$app->request->get('option')) {
            //学生用户审核
            case 'approve':
                $data = '学生用户审核';
                break;
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
}
