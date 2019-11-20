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
    public function actionVote($option)
    {
        if ($option == 'see') {
            //历史列表
            $dataProvider = new ActiveDataProvider([
                'query'=>Vote::find()->where(['and','team_id='.Yii::$app->session['team']->team_id,['or','status=1','status=2']]),
                'pagination' => [
                    'pagesize' => 10
                ],
                'sort' => ['defaultOrder'=>['starttime'=>SORT_DESC]],
            ]);

            return $this->render('vote',['dataProvider'=>$dataProvider]);
        } elseif ($option == 'detail') {
            //参加一次投票活动
            $vote = Vote::findOne(['vote_id'=>Yii::$app->request->get('vote_id')]);

            return $this->render('vote',['vote'=>$vote]);
        } elseif ($option == 'voteone') {
            $vote_id = Yii::$app->request->get('vote_id');
            $wish_id = Yii::$app->request->get('wish_id');
            //验证是否为恶意get刷票
            $user_vote = UserVote::findOne(['vote_id'=>$vote_id,'wish_id'=>$wish_id,'user_id'=>Yii::$app->user->identity->user_id]);
            $surplus = Vote::findOne(['vote_id'=>$vote_id,'status'=>1])->surplus();
            if (($surplus == 0) || $user_vote) {return $this->redirect(['site/error']);}
            $transaction = Yii::$app->db->beginTransaction();
            try {//下次重写时可以试试 $user_vote = new UserVote([''=>,''=>])
                $user_vote = new UserVote();
                $user_vote->user_id = Yii::$app->user->identity->user_id;
                $user_vote->vote_id = $vote_id;
                $user_vote->wish_id = $wish_id;
                if (!$user_vote->save()) {throw new \Exception();}
                $vote_res = VoteRes::findOne(['vote_id'=>$vote_id,'wish_id'=>$wish_id]);
                $vote_res->amount = $vote_res->amount + 1;
                if (!$vote_res->save()) {throw new \Exception();}
                $transaction->commit();
                Yii::$app->session->setFlash('voteoneSuccess');
            } catch (\Exception $e) {
                $transaction->rollback();
                Yii::$app->session->setFlash('voteoneFail');
            }
            return $this->redirect(['team/vote','option'=>'detail','vote_id'=>$vote_id,'team_id'=>Yii::$app->session['team']->team_id]);
        } else {
            throw new NotFoundHttpException("警告！越权操作！");
        }
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
            $dataProvider = new ActiveDataProvider([
                'query'=>Wish::find()->where(['verify_user_id'=>$vote->community->user_id,'status'=>2]),
                'pagination' => [
                    'pagesize' => 10
                ],
            ]);
            if (Yii::$app->request->isPost) {
                //验证想绑定的心愿是否被其它资助者抢先  ！！！ 注意 asArray() 取出来的数组值皆为字符串类型string  ！！！
                $models = Wish::find()->where(['in', 'wish_id', array_keys($_SESSION['vote_wish'])])->asArray()->all();
                foreach ($models as $key => $wish) {
                    if ($wish['status'] !== '2') {// 2 为字符串类型
                        unset($_SESSION['vote_wish'][$wish['wish_id']]);
                        Yii::$app->session->setFlash('blindFail',$wish['wish_id'].'心愿已经被其他资助者抢先绑定了！请重新选择');
                        //直接跳出循环及方法
                        $vote->_endtime = date('y-m-d H:i:s',time()+5*86400);
                        return $this->render('newvote',['vote'=>$vote,'dataProvider'=>$dataProvider]);
                    }
                }
                //验证完 心愿数据并未过期，则验证是否为针对只取一个心愿候选一个心愿投票资助
                $post = Yii::$app->request->post();
                if (count($_SESSION['vote_wish']) == 1 && $post['Vote']['support_num'] == '2') {
                    Yii::$app->session->setFlash('blindFail','候选心愿与计划资助人数矛盾！请重新选择');
                    //直接跳出循环及方法
                    $vote->_endtime = date('y-m-d H:i:s',time()+5*86400);
                    return $this->render('newvote',['vote'=>$vote,'dataProvider'=>$dataProvider]);
                }
                //验证团体 当前余额 是否 大于计划资助所以心愿总期望金额 最小余额
                $minBalance = 0;
                foreach ($_SESSION['vote_wish'] as $key => $wish) {
                    $minBalance = $minBalance + $wish->money;
                }
                if ($minBalance*$vote->community->minpercent*0.01 > Yii::$app->session['team']->balance) {
                    Yii::$app->session->setFlash('blindFail','团体余额不足以启动该资助活动，请为团体充值');
                    $vote->_endtime = date('y-m-d H:i:s',time()+5*86400);
                    return $this->render('newvote',['vote'=>$vote,'dataProvider'=>$dataProvider]);
                }
                //所有验证通过后则load() validate() save()......
                if ($vote->start($post)) {
                    Yii::$app->session->setFlash('start',$vote->vote_id);
                    return $this->redirect(['team/vote','option'=>'see','team_id'=>Yii::$app->session['team']->team_id]);
                }
            } else {
                $vote->_endtime = date('y-m-d H:i:s',time()+5*86400);
            }
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
        //因为没有人写前端，导致视图过多a标签，则后端的数据验证任务繁重而漏洞百出，如若直接url跳至此，因为没写验证又想分割action，就缺少了session和model(vote)，
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
        } elseif ($option == 'endvote') {
            $vote_id = Yii::$app->request->get('vote_id');
            $vote = Vote::findOne(['vote_id'=>$vote_id,'status'=>1]);
            if (!$vote) {throw new NotFoundHttpException('警告！越权操作！');}
            $minBallot = $vote->findMinballot();
            $noVote = $vote->noComplete();
            if (count($vote->res)==1) {
                //如果只有一个候选者心愿则满足统计结果
                //统计结果 发送邮件
                $vote->statistics();
            } elseif ((!$minBallot) && $noVote) {
                //若存在并行最小 并且还有团体成员没有投票
                //发送邮件提醒未投票成员参与
                foreach ($noVote as $k => $user) {
                    $mailer = Yii::$app->mailer->compose('vote',['vote_id'=>$vote->vote_id,'team_name'=>Yii::$app->session['team']->name ,'email'=>$user['email']]);
                    $mailer->setFrom(Yii::$app->params['senderEmail']);
                    $mailer->setTo($user['email']);
                    $mailer->setSubject('人恋人平台-投票活动提醒');
                    $mailer->send();
                }
                Yii::$app->session->setFlash('noMinballot');
            } elseif ((!$minBallot) && (!$noVote)) {
                //若存在并行最小 并且所有成员都参与了投票
                //结束时间延长一天 且 清空当前投票结果
                $vote->reset();
            } else {
                //满足统计结果
                //统计结果 发送邮件
                $vote->statistics();
            }

            return $this->redirect(['team/vote','option'=>'detail','vote_id'=>$vote_id ,'team_id'=>Yii::$app->session['team']->team_id]);
        } else {
            throw new NotFoundHttpException('警告！越权操作！');
        }
    }
}
