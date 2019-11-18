<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\Team;
use app\models\Wish;
use app\models\Vote;
use app\models\VoteRes;
use app\models\Flows;
use app\models\UserTeam;
use app\models\UserVote;
use app\models\Message;
use app\models\Community;
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
                'only' => ['index','myteam','member','newone','finance','support','vote','newvote','editvote'],
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
                        'actions' => ['myteam','member','finance','support','vote','newvote','editvote'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule,$action) {
                            $user_id = Yii::$app->user->identity->user_id;
                            $team_id = Yii::$app->request->get('team_id');
                            $_res = UserTeam::isMember($user_id,$team_id);
                            if ($_res) {
                                Yii::$app->session->set('team',Team::findOne(['team_id'=>$team_id]));
                            }
                            return $_res;
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
     * 以下页面 不可 直接用session 中存的值，必须实时去数据库取，因为session里的数据极有可能过期了
     */
    public function actionMyteam()
    {
        $model = Yii::$app->session->get('team');

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
            $invitation->receiver = '';//与弹框兼容
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
        $balance = Team::findOne(['team_id'=>Yii::$app->session->get('team')->team_id])->balance;
        $model = new Flows();
        $model->scenario = 'rechargeTeam';
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->rechargeTeam($post)) {
                Yii::$app->session->setFlash('rechargeSuccess');
            }
        }
        $models = Flows::find()->where(['or',['and','in_role="teamPurse"','in_id='.Yii::$app->session->get('team')->team_id],['and','out_role="teamPurse"','out_id='.Yii::$app->session->get('team')->team_id]])->orderBy(['createtime'=>SORT_DESC]);
        $count = $models->count();
        $pageSize = Yii::$app->params['pageSize'];
        $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $models = $models->offset($pager->offset)->limit($pager->limit)->all(); //分页处理
        return $this->render('finance',['balance'=>$balance,'model'=>$model,'models'=>$models,'pager'=>$pager]);
    }

    /**
     * 团体成员 查看团体资助的心愿
     */
    public function actionSupport()
    {
        $models = Wish::find()->where(['and','locking_team_id='.Yii::$app->session->get('team')->team_id,['or','status=5','status=6']]);
        $count = $models->count();
        $pageSize = Yii::$app->params['pageSize'];
        $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $models = $models->offset($pager->offset)->limit($pager->limit)->all(); //分页处理
        return $this->render('support',['models'=>$models,'pager'=>$pager]);
    }

    /**
     * 团体投票活动功能
     */
    public function actionVote()
    {

    }

    /**
     * 团体创建者新建一个资助(投票)活动
     */
    public function actionNewvote()
    {
        //只有团体创建者才能发起投票活动
        if (!Yii::$app->user->identity->isCreator()) {throw new NotFoundHttpException('警告！越权操作！');}
        if (!isset($_SESSION['vote_wish'])) {
            $_SESSION['vote_wish'] = [];
        }
        $vote = Vote::findOne(['team_id'=>Yii::$app->session->get('team')->team_id,'status'=>0]);
        if ($vote) {
            $vote->_endtime = date('y-m-d H:i:s',time()+5*86400);
            $dataProvider = new ActiveDataProvider([
                'query'=>Wish::find()->where(['verify_user_id'=>$vote->community->user_id,'status'=>2]),
                'pagination' => [
                    'pagesize' => 10
                ],
            ]);
            return $this->render('newvote',['vote'=>$vote,'dataProvider'=>$dataProvider]);
        } else {
            unset($_SESSION['vote_wish']);//每当新建一个投票时，清除上一个的缓存
            $vote = new Vote(['scenario'=>'newone']);
            $community = Community::find()->where(['status'=>1])->asArray()->all();
            $community = ArrayHelper::map($community,'community_id','community_name');
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                if ($vote->newone($post)) {
                    Yii::$app->session->setFlash('newone');
                    return $this->redirect(['team/newvote','team_id'=>Yii::$app->session->get('team')->team_id]);
                }
            }
            return $this->render('newvote',['vote'=>$vote,'community'=>$community]);
        }
    }

    /**
     * 团体创建者 启动前 编辑一个资助资助(投票)活动
     */
    public function actionEditvote()
    {
        //只有团体创建者才能发起投票活动
        if (!Yii::$app->user->identity->isCreator()) {throw new NotFoundHttpException('警告！越权操作！');}
        $option = Yii::$app->request->get('option');
        if ($option == 'bind') {
            $wish = Wish::findOne(['wish_id'=>Yii::$app->request->get('wish_id')]);
            if ($wish->status !== 2) {
                Yii::$app->session->setFlash('blindFail','该心愿已经被其他资助者抢先绑定了！请重新选择');
            } else {
                if (isset($_SESSION['vote_wish'][$wish->wish_id])) {
                    unset($_SESSION['vote_wish'][$wish->wish_id]);
                } else{
                    $_SESSION['vote_wish'][$wish->wish_id] = $wish;
                }
            }
            return $this->redirect(['team/newvote','team_id'=>Yii::$app->session->get('team')->team_id]);
        } elseif ($option == 'del') {
            $vote = Vote::findOne(['status'=>0,'team_id'=>Yii::$app->session['team']->team_id]);
            if (!$vote) {throw new NotFoundHttpException('出现了一点问题.....');}
            $vote->delete();
            unset($_SESSION['vote_wish']);//删除正在编辑的投票活动，则清除缓存
            return $this->redirect(['team/newvote','team_id'=>Yii::$app->session->get('team')->team_id]);
        } else {
            throw new NotFoundHttpException('警告！越权操作！');
        }
    }
}
