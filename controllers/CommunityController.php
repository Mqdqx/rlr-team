<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\Community;

class CommunityController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            //无权限访问过滤且报错
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['error'],
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['?'],
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
     * Displays community homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = Yii::$app->user->isGuest ? 'site' :Yii::$app->user->identity->role;
        $id = Yii::$app->request->get('id');
        if ($id) {
            $community = Community::findOne(['community_id'=>$id]);
            if (!$community) {throw new NotFoundHttpException("警告，越权操作！");}
            return $this->render('index',['community'=>$community]);
        } else {
            $models = Community::find()->where(['status'=>1]);
            $count = $models->count();
            $pageSize = 12;
            $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
            $models = $models->offset($pager->offset)->limit($pager->limit)->all(); //分页处理
            return $this->render('index',['models'=>$models,'pager'=>$pager]);
        }

    }
    
    public function actionError()
    {
        
        //return $this->redirect(['site/error']);
    }
}
