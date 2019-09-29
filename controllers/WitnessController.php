<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\Wish;

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
        
        return $this->render('personalinfo');
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
        switch (Yii::$app->request->get('option')) {
            //未激活心愿和生成心愿码
            case 'noactivate':
                $models = Wish::find_Witness('noactivate');
                break;
            //待审心愿
            case 'waiting':
                
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