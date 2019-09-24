<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;

class StudentController extends Controller
{
    /**
     * 指定当前控制器的模板
     */
    public $layout = 'student';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            //无权限访问过滤且报错
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','finance','wish','upgrade'],
                'rules' => [
                    [
                        'actions' => ['index','finance','wish','upgrade'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule,$action) {
                            $_role = Yii::$app->user->identity->role;
                            return ($_role !== 'student') ? ($this->redirect(['site/error'])) : true;
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
     * 学生 钱包流水 功能：1.体现，2.查看流水
     */
    public function actionFinance()
    {
        //
        $data = '钱包流水';
        return $this->render('finance',['data'=>$data]);
    }

    /**
     * 学生 我的心愿 功能：1.查看关于自己的心愿，2.发布一个心愿
     */
    public function actionWish()
    {
        //由get传参右侧横向导航option的不同渲染不同功能
        switch (Yii::$app->request->get('option')) {
            //查看心愿
            case 'see':
                $data = '我的心愿';
                break;
            //发布一个心愿    
            case 'newone':
                $data = '发布心愿';
                break;
            //其它传参报错
            default:
                throw new NotFoundHttpException("警告！越权操作！");
                break;
        }
        return $this->render('wish',['data'=>$data]);
    }

    /**
     * 学生 升级为资助者 功能：必须在完成心愿状态下操作
     */
    public function actionUpgrade()
    {
        //
        $data = '升级为资助者';
        return $this->render('upgrade',['data'=>$data]);
    }
}
