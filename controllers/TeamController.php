<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use app\models\Team;
use app\models\Wish;
use app\models\Vote;
use app\models\VoteRes;
use app\models\Flows;
use app\models\UserTeam;
use app\models\UserVote;
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
                'only' => ['index','myteam','member','newone','finance','support','vote','newvote'],
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
                        'actions' => ['myteam','member','finance','support','vote','newvote'],
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
     * 以下页面 不可 直接用session 中存的值，必须实时去数据库取，因为session里的数据极有可能过期了
     */
    public function actionMyteam()
    {
        $model = Team::findOne(['team_id'=>Yii::$app->request->get('team_id')]);
        //必须验证数据的真实性和时效性  本文件Line54
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
        //该团体所有的投票活动 列表
        if (Yii::$app->request->get('option') == 'see') {
            $models = Vote::find()->where(['status'=>1,'team_id'=>Yii::$app->session->get('team')->team_id])->orderBy(['starttime'=>SORT_DESC]);
            $count = $models->count();
            $pageSize = Yii::$app->params['pageSize'];
            $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
            $models = $models->offset($pager->offset)->limit($pager->limit)->all(); //分页处理
            return $this->render('vote',['models'=>$models,'pager'=>$pager]);
        //进入一个 状态为 进行中的投票活动
        } elseif (Yii::$app->request->get('option') == 'vote') {
            $vote = Vote::findOne(['vote_id'=>Yii::$app->request->get('vote_id'),'status'=>1]);
            if (!$vote) {throw new NotFoundHttpException('该投票活动可能已经结束，请刷新后重试！');}
            return $this->render('vote',['vote'=>$vote]);
        //当前登录用户投给 一位候选人心愿一票
        } elseif (Yii::$app->request->get('option') == 'voteone') {
            $vote = Vote::findOne(['vote_id'=>Yii::$app->request->get('vote_id'),'status'=>1]);
            $wish = Wish::findOne(['wish_id'=>Yii::$app->request->get('wish_id')]);
            //必须验证传来的数据/请求的过期性和伪造性
            if (!$vote || !$wish) {return $this->redirect(['site/error']);}
            //验证传入心愿是否与此投票活动绑定
            $voteRes = VoteRes::findOne(['vote_id'=>$vote->vote_id,'wish_id'=>$wish->wish_id]);
            if (!$voteRes) {throw new NotFoundHttpException('警告！越权操作！');}
            //验证此用户是否已经为此投票活动的此心愿投过一票了
            $userVote = UserVote::findOne(['user_id'=>Yii::$app->user->identity->user_id,'vote_id'=>$vote->vote_id,'wish_id'=>$wish->wish_id]);
            if ($userVote) {throw new NotFoundHttpException('警告！越权操作！');}
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $userVote = new UserVote();
                $userVote->user_id = Yii::$app->user->identity->user_id;
                $userVote->vote_id = $vote->vote_id;
                $userVote->wish_id = $wish->wish_id;
                if (!$userVote->save()) {
                    throw new \Exception();
                }
                $voteRes->amount += 1;
                if (!$voteRes->save()) {
                    throw new \Exception();
                }
                $transaction->commit();
                Yii::$app->session->setFlash('voteoneSuccess');
            } catch (\Exception $e) {
                $transaction->rollback();
                Yii::$app->session->setFlash('voteoneFail');
            }
            return $this->redirect(Yii::$app->request->getReferrer());
        //团体创建者 即刻结束投票且统计投票结果
        } elseif (Yii::$app->request->get('option') == 'endvote') {
            //判断 团体创建者，投票团队状态 和 归属 是否一一对应
            $vote = Vote::findOne(['vote_id'=>Yii::$app->request->get('vote_id'),'status'=>1,'team_id'=>Yii::$app->session->get('team')->team_id]);
            if (!Yii::$app->session->get('team')->isCreator() || !$vote) {throw new NotFoundHttpException('警告！越权操作！');}
            //统计此次投票活动结果 且 状态设为 重启 或 结束
            if (!$vote->statistics()) {
                return $this->redirect(Yii::$app->request->getReferrer());
            }
            return /*$this->redirect(['team/vote','team_id'=>Yii::$app->session->get('team')->team_id,'option'=>'see'])*/;//跳转到 结果界面更好点
        } else {
            return $this->redirect(['site/error']);
        }
    }

    /**
     * 团体创建者 发起一个 投票活动 功能
     */
    public function actionNewvote()
    {
        //只有团体创建者才能发起投票活动
        if (!Yii::$app->user->identity->isCreator()) {
            throw new NotFoundHttpException('警告！越权操作！');
        }
        //待编辑 未启动的 投票活动
        if (Yii::$app->request->get('option') == 'see') {
            $query = Vote::find()->where(['team_id'=>Yii::$app->session->get('team')->team_id,'status'=>0]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pagesize' => 10
                ],
            ]);
            return $this->render('newvote',['dataProvider'=>$dataProvider]);
        //新建一个投票活动
        } elseif (Yii::$app->request->get('option') == 'new') {
            $model = new Vote(['scenario'=>'newone']);
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                if ($model->newone($post)) {
                    Yii::$app->session->setFlash('newone');
                    return $this->redirect(['team/newvote','option'=>'see','team_id'=>Yii::$app->session->get('team')->team_id]);
                }
            }
            return $this->render('newvote',['model'=>$model]);
        //编辑 启动一个 投票活动
        } elseif (Yii::$app->request->get('option') == 'edit') {
            //$model = Vote::findOne(['vote_id'=>Yii::$app->request->get('vote_id')]);
            foreach (Yii::$app->session->get('team')->votes as $key => $vote) {
                if ($vote->vote_id == Yii::$app->request->get('vote_id')) {
                    Yii::$app->session->get('team')->_vote = $vote;
                    $model = $vote;
                }
            }
            //剔除session 中 暂时绑定投票的心愿中状态 不为 待资助的 $status 不等于2的
            foreach ($model->vote_wish as $key => $wish) {
                if (Wish::findOne(['wish_id'=>$wish->wish_id])->status !== 2) {
                    unset($model->vote_wish[$wish->wish_id]);
                }
            }
            $model->scenario = 'start';
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                if ($model->start($post)) {
                    Yii::$app->session->setFlash('start',$model->vote_id);
                    return $this->redirect(['team/newvote','option'=>'see','team_id'=>Yii::$app->session->get('team')->team_id]);
                }
            } else {
                $model->_endtime = date('y-m-d H:i:s',time()+86400);
            }
            //心愿列表
            $query = Wish::find()->where(['status'=>2]);
            $dataProvider = new ActiveDataProvider([
                'query'=>$query,
                'pagination'=>['pagesize'=>10],
            ]);
            $models = $query->all();//心愿流变弹出框
            return $this->render('newvote',['model'=>$model,'models'=>$models,'dataProvider'=>$dataProvider]);
        //为一个投票活动 绑定或解绑一个 候选心愿
        } elseif (Yii::$app->request->get('option') == 'bind') {
            $wish = Wish::findOne(['wish_id'=>Yii::$app->request->get('wish_id')]);
            if (!$wish) {
                throw new NotFoundHttpException('操作失败，请稍后再试或反馈此问题');
            }
            if ($wish->status !== 2) {
                Yii::$app->session->setFlash('overdue');
                return $this->redirect(Yii::$app->request->getReferrer());
            }
            $_key = false;
            foreach (Yii::$app->session->get('team')->_vote->vote_wish as $key => $value) {
                if ($value->wish_id == $wish->wish_id) {
                    $_key = $key;
                }
            }
            //必须使用变量$_key做 入选/剔除 依据, 因为$key 受遍历影响 最后与$_key 值不同
            if ($_key) {
                unset(Yii::$app->session->get('team')->_vote->vote_wish[$_key]);
            } else {
                if (count(Yii::$app->session->get('team')->_vote->vote_wish) == Yii::$app->session->get('team')->_vote->candidate_num) {
                    Yii::$app->session->setFlash('overflow');
                } else {
                    Yii::$app->session->get('team')->_vote->vote_wish[$wish->wish_id] = $wish;
                }
            }
            return $this->redirect(Yii::$app->request->getReferrer());
        }
    }

}
