<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AjaxFilter;
use yii\data\Pagination;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use app\models\Community;
use app\models\Message;
use app\models\TeamMessage;
use app\models\Guardian;
use yiier\region\models\Region;
use yii\helpers\ArrayHelper;

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
            [
                'class' => AjaxFilter::className(),//非AJAX过滤器,
                'only' => ['getcity','getcommunity']
            ],
            //无权限访问过滤且报错
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout','message','contact','personalinfo','register_vip','register_community','getcity','find_password'],
                'rules' => [
                    [
                        'actions' => ['logout','message','contact','personalinfo','getcity'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['register_vip','register_community','find_password'],
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
            if (Yii::$app->user->identity->status == 4 || Yii::$app->user->identity->status == 5) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('noActivate');
                return $this->render('error');
            }
            if (Yii::$app->request->get('option') == 'jointeam') {
                return $this->redirect(['site/message','option'=>'handle','message_id'=>Yii::$app->request->get('message_id')]);
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
        $model->scenario = "register";
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
        $model->scenario = "register";
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
                $models = Message::find()->where(['to'=>Yii::$app->user->identity->user_id])->orderBy(['sendtime'=>SORT_DESC]);
                break;
            //已发信息
            case 'sent':
                $models = Message::find()->where(['from'=>Yii::$app->user->identity->user_id])->orderBy(['sendtime'=>SORT_DESC]);
                break;
            //发送信息
            case 'send':
                $model = new Message();
                $model->scenario = 'send';
                if (Yii::$app->request->isPost) {
                    $post = Yii::$app->request->post();
                    if ($model->send($post)) {
                        Yii::$app->session->setFlash('send');
                        return $this->refresh();
                    }
                }
                return $this->render('message',['model'=>$model]);
                break;
            //标记已经读
            case 'read':
                $message_id = Yii::$app->request->get('message_id');
                $message = Message::findOne(['message_id'=>$message_id,'to'=>Yii::$app->user->identity->user_id]);
                if (!$message) {
                    throw new NotFoundHttpException("警告！越权操作！");
                }
                $message->status = ($message->status == 1) ? 2 : 1;
                if ($message->save()) {
                    return $this->redirect(['site/message','option'=>'receive']);
                }
                break;
            //处理一条邀请信息
            case 'handle':
                $message_id = Yii::$app->request->get('message_id');
                $message = Message::findOne(['message_id'=>$message_id,'to'=>Yii::$app->user->identity->user_id]);
                if (!$message) {  //过滤篡改GET
                    throw new NotFoundHttpException("警告！越权操作！");
                }
                if (Yii::$app->request->get('decision') == 'agree' || Yii::$app->request->get('decision') == 'reject') {
                    if ($message->status !== 0) {
                        throw new NotFoundHttpException("警告！越权操作！");
                    }
                    if(!$message->decide(Yii::$app->request->get('decision'))) {
                        throw new NotFoundHttpException("服务器错误，请稍后再试或反馈此问题！");
                    }
                }
                return $this->render('message',['message'=>$message]);
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
        return $this->render('message',['models'=>$models,'pager'=>$pager]);
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
                if (Yii::$app->user->identity->guardian) {
                    $model = Yii::$app->user->identity->guardian;
                } else {
                    $model = new Guardian();
                    $model->scenario = 'updateGuardian'; 
                    if (Yii::$app->request->isPost) {
                        $post = Yii::$app->request->post();
                        if ($model->updateGuardian($post)) {
                            Yii::$app->session->setFlash('guardianUpdate');
                            return $this->refresh();
                        }
                    }
                }
                break;
            case 'updateGuardian':
                $model = Yii::$app->user->identity->guardian;
                $model->scenario = 'updateGuardian'; 
                if (Yii::$app->request->isPost) {
                    $post = Yii::$app->request->post();
                    if ($model->updateGuardian($post)) {
                        Yii::$app->session->setFlash('guardianUpdate');
                        return $this->redirect(['site/personalinfo','option'=>'guardian']);
                    }
                }
                break;
            default:
                //option == see;
                break;
        }
        return $this->render('personalinfo',['model'=>$model]);
    }

    /**
     * 用于注册后激活账号：vip或community
     */
    public function actionUseractivate()
    {
        //验证get请求传参
        $email = Yii::$app->request->get('email');
        $token = Yii::$app->request->get('token');
        $user = User::findOne(['email'=>$email,'token'=>$token]);
        if (!$user) {
            throw new NotFoundHttpException("无效访问！");
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

    /**
     * 用于受邀请后注册激活账号：vip
     */
    public function actionUserregister()
    {
        $email = Yii::$app->request->get('email');
        $token = Yii::$app->request->get('token');
        $user = User::findOne(['email'=>$email,'token'=>$token]);
        if (!$user) {
            throw new NotFoundHttpException("无效访问！");
        }
        if ($user->status !==5) {
            throw new NotFoundHttpException("该账号已经注册激活，请勿重复操作！");
        }
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }
        $user->scenario = 'invitation';
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($user->invitation($post)) {
                Yii::$app->user->login($user,3600*24*30);
                Yii::$app->session->setFlash('activated');
                return $this->render('error');
            }
        }
        return $this->render('invitation',['model'=>$user]);
    }

    /**
     * 用于 用户 收到平台(代)发的邮件后 点击链接挑战到对应的操作活动
     */
    public function actionJump()
    {
        switch (Yii::$app->request->get('option')) {
            case 'jointeam':
                $team_message_id = Yii::$app->request->get('team_message_id');
                $teamMessage = TeamMessage::findOne(['id'=>$team_message_id]);
                if (!$teamMessage) {
                    throw new NotFoundHttpException("无效访问！");
                }
                if (!Yii::$app->user->isGuest) {  //如果处于已经登录状态
                    if (Yii::$app->user->identity->user_id == $teamMessage->message->toUser->user_id) {
                        return $this->redirect(['site/message','option'=>'handle','message_id'=>$teamMessage->message->message_id]);
                    }
                    Yii::$app->user->logout();//如果当前登录的用户非链接跳转来的用户，则注销
                }
                return $this->redirect(['site/login','option'=>'jointeam','message_id'=>$teamMessage->message->message_id]);
                break;
            
            default:
                throw new NotFoundHttpException("未知错误");
                break;
        }

    }

    /**
     * 响应前台AJAX取出省对应市的请求
     */
    public function actionGetcity()
    {
        $province_id = Yii::$app->request->get('province_id');
        $city = Region::find()->where(['type'=>1,'parent_id'=>$province_id])->asArray()->all();
        $city = ArrayHelper::map($city,'id','name');
        echo json_encode($city);
    }

    /**
     * 响应主页检索对应城市的社区
     */
    public function actionGetcommunity()
    {
        $city = Yii::$app->request->get('city');
        $city = (new \yii\db\Query)->select(['id'])->where(['and','type=1',['like','name',$city]])->from('region')->one();
        $communitys = Community::find()->where(['city_id'=>$city['id']])->asArray()->all();
        echo json_encode($communitys);
    }

    /**
     * 忘记密码邮件重置
     */
    public function actionFind_password()
    {
        $option = Yii::$app->request->get('option');
        if ($option == 'send') {
            $user = new User(['scenario'=>'send']);
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                if ($user->load($post) && $user->validate()) {
                    $user = User::findOne(['email'=>$user->email]);
                    $user->token = $user->createToken();
                    if ($user->save()) {
                        $mailer = Yii::$app->mailer->compose('find_password',['email'=>$user->email,'token'=>$user->token]);
                        $mailer->setFrom(Yii::$app->params['senderEmail']);
                        $mailer->setTo($user->email);
                        $mailer->setSubject("人恋人平台-密码重置");
                        $mailer->send();
                        Yii::$app->session->setFlash('find_password','发送成功，请前往邮箱链接重置密码！');
                        return $this->redirect(['site/find_password','option'=>'tip']);
                    }
                }
            }
        } elseif ($option == 'reset') {
            $email = Yii::$app->request->get('email');
            $token = Yii::$app->request->get('token');
            $user = User::findOne(['email'=>$email,'token'=>$token]);
            if (!$user) {throw new NotFoundHttpException("无效访问！");}
            $user->password = '';
            $user->scenario = 'reset';
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                if ($user->load($post) && $user->validate()) {
                    $user->password = password_hash($user->password, PASSWORD_DEFAULT);
                    //用于后续save时保证可以通关验证
                    $user->repassword = $user->password;
                    $user->loginip = $user->getIp();
                    $user->logintime = time();
                    $user->token = $user->createToken();
                    if ($user->save()) {
                        Yii::$app->session->setFlash('find_password','密码重置成功，之后请用新密码登录！');
                        return $this->redirect(['site/find_password','option'=>'tip']);
                    }
                }
            }
        } elseif ($option == 'tip') {
            $user = null;
        } else {
            throw new NotFoundHttpException("无效访问！");
        }
        return $this->render('find_password',['user'=>$user]);
    }
}
