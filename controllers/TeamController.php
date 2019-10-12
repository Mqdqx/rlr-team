<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\Team;
use app\models\UserTeam;
use app\models\Message;
use app\models\TeamMessage;

class TeamController extends Controller
{
    /**
     * 指定当前控制器的模板
     */
    public $layout = 'vip';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            //无权限访问过滤且报错
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','myteam','member','newone','finance'],
                'rules' => [
                    [
                        'actions' => ['index','newone'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule,$action) {
                            $_role = Yii::$app->user->identity->role;
                            return ($_role !== 'vip') ? ($this->redirect(['site/error'])) : true;
                        }
                    ],
                    [
                        'actions' => ['myteam','member','finance'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule,$action) {
                            $user_id = Yii::$app->user->identity->user_id;
                            $team_id = Yii::$app->request->get('team_id');
                            return (UserTeam::isMember($user_id,$team_id)) ? true : false;
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
     * Displays team homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //$models = Team::find()->where(['user_id'=>Yii::$app->user->identity->user_id]);
        $models = Yii::$app->user->identity->getTeams();
        $count = $models->count();
        $pageSize = Yii::$app->params['pageSize'];
        $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $models = $models->offset($pager->offset)->limit($pager->limit)->all(); //分页处理
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
    
    /**
     * 进入我创建的团体 或者 我创建团体
     * 团体信息
     */
    public function actionMyteam()
    {
        $model = Team::findOne(['team_id'=>Yii::$app->request->get('team_id')]);
        Yii::$app->session->set('team',$model);
        return $this->render('myteam',['model'=>$model]);
    }

    /**
     * vip用户新建一个团体
     */
    public function actionNewone()
    {
        $model = new Team();
        $model->scenario = 'newone';
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->newone($post)) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('newTeam',$model->name);
                    return $this->redirect(['team/index']);
                }
            } catch (\Exception $e) {
                $transaction->rollback();
            }
        }
        return $this->render('newone',['model'=>$model]);
    }

    /**
     * 团体 成员功能
     * 成员列表|邀请成员
     */
    public function actionMember()
    {
        $invitedUsers = TeamMessage::findAll(['team_id'=>Yii::$app->session->get('team')->team_id]);//已经邀请了的用户
        $invitation = new Message();
        $invitation->scenario = 'joinTeam';
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($invitation->joinTeam($post)) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('inviteJoinTeam',$invitation->_toUser->username);
                }
            } catch (\Exception $e) {
                $transaction->rollback();
            }
        }
        $models = Team::find()->where(['team_id'=>Yii::$app->request->get('team_id')])->one()->getMember();
        $count = $models->count();
        $pageSize = Yii::$app->params['pageSize'];
        $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $models = $models->offset($pager->offset)->limit($pager->limit)->all(); //分页处理
        return $this->render('member',['models'=>$models,'pager'=>$pager,'invitation'=>$invitation,'invitedUsers'=>$invitedUsers]);
    }

    /**
     * 团体 财务功能
     * 为团体充值|团体流水
     */
    public function actionFinance()
    {
        

        return $this->render('finance');
    }

}
