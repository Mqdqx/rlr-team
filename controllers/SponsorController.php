<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;

class SponsorController extends Controller
{
    /**
     * 指定当前控制器的模板
     */
    public $layout = 'sponsor';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            //无权限访问过滤且报错
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','finance','support','wish'],
                'rules' => [
                    [
                        'actions' => ['index','finance','support','wish'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule,$action) {
                            $_role = Yii::$app->user->identity->role;
                            return ($_role !== 'sponsor') ? ($this->redirect(['site/error'])) : true;
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
     * 个人钱包流水功能:充值，体现，查询流水
     */
    public function actionFinance()
    {
        //
        $data = '钱包流水';
        return $this->render('finance',['data'=>$data]);
    }

    /**
     * 资助者：我的资助 查看功能
     */
    public function actionSupport()
    {
        //
        $data = '我的资助';
        return $this->render('support',['data'=>$data]);
    }

    /**
     * 资助者：查看对应范围(社区)学生发布的心愿
     */
    public function actionWish()
    {
        //
        $data = '心愿广场';
        return $this->render('wish',['data'=>$data]);
    }

}
