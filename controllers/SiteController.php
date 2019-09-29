<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;

class SiteController extends Controller
{
    /**
     * 指定当前控制器的模板
     */
    public $layout = 'site';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            //无权限访问过滤且报错
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout','message','contact','personalinfo','register_vip','register_community'],
                'rules' => [
                    [
                        'actions' => ['logout','message','contact','personalinfo'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['register_vip','register_community'],
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //重新定义视图模板以适应各种角色用户
        $this->layout = (Yii::$app->user->isGuest) ? 'site' : Yii::$app->user->identity->role;
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->user->identity->logintime = time();
            Yii::$app->user->identity->loginip = $model->getIp();
            Yii::$app->user->identity->save();
            if (Yii::$app->user->identity->status == 4) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('noActivate');
                return $this->render('error');
            }
            //跳转到各种用户（角色）的应用中心（控制器）
            return $this->redirect([Yii::$app->user->identity->role.'/index']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        //重新定义视图模板以适应各种角色用户
        $this->layout = Yii::$app->user->identity->role;
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        //重新定义视图模板以适应各种角色用户
        $this->layout = (Yii::$app->user->isGuest) ? 'site' : Yii::$app->user->identity->role;
        return $this->render('about');
    }

    /**
     * vip用户注册界面
     * 邮箱注册
     */
    public function actionRegister_vip()
    {
        $model = new User;
        //如有post提交，调用模型处理数据(库)
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->register('vip',$post)) {
                Yii::$app->session->setFlash('successRegister');
                return $this->refresh();
            }
        }
        $model->password = '';
        $model->repassword = '';
        return $this->render('register',['model'=>$model]);
    }

    /**
     * witness用户社区管理员注册界面
     * 邮箱注册
     */
    public function actionRegister_community()
    {
        $model = new User;
        //如有post提交，调用模型处理数据(库)
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->register('witness',$post)) {
                Yii::$app->session->setFlash('successRegister');
                return $this->refresh();
            }
        }
        $model->password = '';
        $model->repassword = '';
        return $this->render('register',['model'=>$model]);
    }

    /**
     * 内外通信功能页
     */
    public function actionMessage()
    {
        //重新定义视图模板以适应各种角色用户
        $this->layout = Yii::$app->user->identity->role;
        //判断横向导航栏的选项(get请求)传递到不同的action
        switch (Yii::$app->request->get('option')) {
            //收件箱
            case 'receive':
                $data = '收件箱信息';
                return $this->render('message',['data'=>$data]);
                break;
            //已发信息
            case 'sent':
                $data = '已发送的信息';
                return $this->render('message',['data'=>$data]);
                break;
            //发送信息
            case 'send':
                $data = '发送信息';
                return $this->render('message',['data'=>$data]);
                break;
            //其它传参抛出异常
            default:
                throw new NotFoundHttpException("警告！越权操作！");
                break;
        }
    }

    /**
     * 公共功能-个人信息功能：查看，完善
     */
    public function actionPersonalinfo()
    {
        //重新定义视图模板以适应各种角色用户
        $this->layout = Yii::$app->user->identity->role;
        $model = Yii::$app->user->identity;
        switch (Yii::$app->request->get('option')) {
            case 'modify':
                if (Yii::$app->request->isPost) {
                    $post = Yii::$app->request->post();
                    if ($model->modify($post)) {
                        Yii::$app->session->setFlash('modifySuccess','修改完善成功!');
                        return $this->refresh();
                    }
                }
                break;
            case 'guardian':
                
                break;
            default:

                break;
        }
        return $this->render('personalinfo',['model'=>$model]);
    }

    /**
     * 用于注册后激活账号：vip/community
     */
    public function actionUseractivate()
    {
        //验证get请求传参
        $email = Yii::$app->request->get('email');
        $token = Yii::$app->request->get('token');
        $user = User::findOne(['email'=>$email,'token'=>$token]);
        if (!$user) {
            throw new NotFoundHttpException("警告！伪造请求！你的信息已经记录！");
        }
        if ($user->status !== 4) {
            throw new NotFoundHttpException("该账号已经激活，请勿重复操作！");
        }
        $user->status = ($user->role == 'vip') ? 1 : 3;
        $user->logintime = time();
        $user->loginip = $user->getIp();
        if ($user->save()) {
            if (!Yii::$app->user->isGuest) {
                Yii::$app->user->logout();
            }
            Yii::$app->user->login($user,3600*24*30);
            Yii::$app->session->setFlash('activated');
            return $this->render('error');
        }
    }

}
