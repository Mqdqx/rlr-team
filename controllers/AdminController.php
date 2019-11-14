<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\User;
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
        $data = '平台余额和流水';
        return $this->render('finance',['data'=>$data]);
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
        //
        $data = '团体管理';
        return $this->render('team',['data'=>$data]);
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
