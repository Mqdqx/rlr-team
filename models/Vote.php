<?php

namespace app\models;

use Yii;
use app\models\Wish;
use app\models\Team;
use app\models\Community;
use app\models\VoteRes;
use app\models\UserVote;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "vote".
 *
 * @property int $vote_id 一次投票活动的主键ID
 * @property int $team_id 隶属团体的ID
 * @property int $community_id 资助心愿所属社区ID
 * @property string $title 此次投票的标题
 * @property int $support_num 最终资助的人数
 * @property int $candidate_num 候选人数
 * @property int $createtime 创建投票时间戳
 * @property int $starttime 开始投票时间戳
 * @property int $endtime 自动结束时间戳
 * @property int $status 实时状态：0->未开始，1->投票中，2->投票已结束
 * @property int $version 版本号（乐观锁）
 */
class Vote extends \yii\db\ActiveRecord
{
    const VOTEING = 1;
    const FINISHED = 2;

    public static $_status = [
        self::VOTEING => '正在进行',
        self::FINISHED => '已结束',
    ];

    /**
     * 定义临时量 可能存于session使用
     */
    public $vote_wish;//存 Wish 对象数组 编辑/启动投票钱的临时捆绑
    public $_endtime;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vote';
    }

    /**
     * 乐观锁
     * @return string
     */
    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title','community_id'],'required','on'=>['newone']],
            [['title'],'string','max'=>30,'on'=>['newone']],

            [['support_num','candidate_num','_endtime'],'required','on'=>['start']],
            ['_endtime','safe','on'=>'start'],

            [['team_id', 'community_id','support_num', 'candidate_num', 'createtime','starttime', 'endtime', 'status','version'], 'integer'],
            [['title'], 'string', 'max' => 255],
            ['_endtime','safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vote_id' => '投票编号',
            'team_id' => '隶属团体的ID',
            'community_id' => '资助心愿所属社区',
            'title' => '投票标题',
            'support_num' => '最终资助的人数',
            'candidate_num' => '候选人数',
            'createtime' => '创建时间',
            'starttime' => '开始投票时间',
            'endtime' => '自动结束时间',
            '_endtime' => '自动结束时间',
            'status' => '当前状态',
            'version' => '版本号（乐观锁）',
        ];
    }

    /**
     * 关联一对多的 心愿 | Wish 对象
     */
    public function getWishs()
    {
        return $this->hasMany(Wish::className(),['wish_id'=>'wish_id'])->viaTable(VoteRes::tableName(),['vote_id'=>'vote_id']);
    }

    /**
     * 关联一对一的 团体
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(),['team_id'=>'team_id']);
    }

    /**
     * 关联一对一的 心愿所属社区
     */
    public function getCommunity()
    {
        return $this->hasOne(Community::className(),['community_id'=>'community_id']);
    }

    /**
     * 关联一对多的 结果
     */
    public function getRes()
    {
        return $this->hasMany(VoteRes::className(),['vote_id'=>'vote_id']);
    }

    /**
     * 格式化显示临时捆绑的的候选人心愿，重写后 抛弃此方法
     */
    /*public function vote_wish()
    {
        $info = '';
        if (count($this->vote_wish) == 0) {
            $info = '  <span style="color:red">请从下列表取出'.$this->candidate_num.'个候选者心愿</span>  ';
        } else {
            foreach ($this->vote_wish as $key => $value) {
                $info .= "  <button class=\"btn btn-info btn-xs\" data-toggle=\"modal\" data-target=\"#".$value->wish_id."\">".$value->wish_id."</button>  ";
            }
            if (($this->candidate_num - count($this->vote_wish))) {
                $info .= '  <span style="color:red">还差'.($this->candidate_num - count($this->vote_wish)).'个候选者心愿</span>  ';
            }
        }
        return $info;
    }*/

    /**
     * 投票活动 实时 肩负 金额 
     * 放弃此 方法， 因为session过期性没有考虑
     */
    /*public function getMoney()
    {
        $money = 0;
        if (!Yii::$app->session->get('team')->_vote) {
            foreach ($this->wishs as $key => $wish) {
                $money += $wish->money;
            }
            return $money;
        }
        foreach (Yii::$app->session->get('team')->_vote->vote_wish as $key => $wish) {
            $money += $wish->money;
        }
        return $money;
    }*/

    /**
     * 表格行颜色
     */
    public function getColor()
    {
        $status = $this::$_status[$this->status];
        if ($status == '已结束') {
            return 'success';
        } elseif ($status == '正在进行') {
            return 'info';
        } elseif ($status == '提前结束') {
            return 'warning';
        }
    }

    /**
     * 团体创建者 创建一个新的投票
     */
    public function newone($data)
    {
        if ($this->load($data) && $this->validate()) {
            $this->createtime = time();
            $this->team_id = Yii::$app->session->get('team')->team_id;
            $this->status = 0;
            return (bool)$this->save();
        }
        return false;
    }

    /**
     * 团体创建者 启动 一个投票
     */
    public function start($data)
    {
        $this->scenario = 'start';
        if ($this->load($data) && $this->validate()) {
            $this->endtime = strtotime($this->_endtime);//日期格式的字符串 转 时间戳
            $this->status = 1;
            $this->starttime = time();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$this->save()) {
                    throw new \Exception();
                }
                foreach ($_SESSION['vote_wish'] as $key => $wish) {
                    $wish->locking_team_id = $this->team_id;
                    $wish->status = 7;
                    $wish->locking_time = time();
                    if (!$wish->save()) {
                        throw new \Exception();
                    }
                    $voteRes = new VoteRes();
                    $voteRes->vote_id = $this->vote_id;
                    $voteRes->wish_id = $wish->wish_id;
                    if (!$voteRes->save()) {
                        throw new \Exception();
                    }
                }
                $transaction->commit();
                return true;
            } catch (\Exception $e) {
                $transaction->rollback();
                Yii::$app->session->setFlash('startFail',$this->vote_id);
                return false;//此为 存库(事务)失败 ，可能是因为数据过期
            }
        }
        return false;//此为数据验证失败
    }

    /**
     * 用于前端显示对于一个心愿时的投票按钮
     * user_id当前参与投票的用户
     * wish_id 面向的心愿
     */
    public function showButton($wish_id)
    {
        $user_id = Yii::$app->user->identity->user_id;
        $user_vote  = UserVote::find()->where(['user_id'=>$user_id,'vote_id'=>$this->vote_id])->all();
        foreach ($user_vote as $key => $value) {
            if ($value->wish_id == $wish_id) {
                return 'voted';//已投过
            }
        }
        if (count($user_vote) == $this->support_num) {
            return 'insufficient';//票数用完
        }
        return 'vote';
    }

    /**
     * 通过 user_id 判断该用户对于此次投票还有 几张票
     */
    public function surplus()
    {
        $user_id = Yii::$app->user->identity->user_id;
        $num = count(UserVote::find()->where(['user_id'=>$user_id,'vote_id'=>$this->vote_id])->all());
        return ($this->support_num - $num);
    }

    /**
     * 判断是不是所有参与团体成员都投完了票
     * 全投完返回 false,否则返回未投成员[[User]]数组asArray()
     */
    public function noComplete()
    {
        $noVote = [];
        $poll = UserVote::find()->where(['vote_id'=>$this->vote_id])->asArray()->all();
        $poll = ArrayHelper::map($poll,'id','user_id');
        foreach (Yii::$app->session['team']->getMember()->asArray()->all() as $key => $user) {
            if (!in_array($user['user_id'], $poll)) {
                $noVote[$user['user_id']] = $user;
            }
        }
        if (count($noVote) == 0) {
            return false;
        }
        return $noVote;
    }

    /**
     * 统计结果是 取出票数最少的心愿Wish，存在并行最小则 返回false
     */
    public function findMinballot()
    {
        $minBallot = [];
        $res = $this->res;
        $minBallot[0] = $res[0];
        for ($i=1; $i < count($res); $i++) { 
            if ($res[$i]->amount < $minBallot[0]->amount) {
                $minBallot = [];
                $minBallot[0] = $res[$i];
            } elseif ($res[$i]->amount == $minBallot[0]->amount) {
                $minBallot[1] = $res[$i];
            }
        }
        if (count($minBallot) == 2) {
            return false;
        }
        return $minBallot[0];
    }

    /**
     * 无多个最低候选 情况下 统计结果
     */
    public function statistics()
    {
        //开始逐步处理各心愿
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->status = 2;//改变此投票的状态为 已经结束
            if (!$this->save()) {throw new \Exception();}
            //voteRes 数组重建 wish_id=>(object)VoteRes
            $_res = [];
            foreach ($this->res as $k => $voteres) {
                $_res[$voteres->wish_id] = $voteres;
            }
            foreach ($this->wishs as $key => $wish) {
                if ($wish->wish_id == $this->findMinballot()->wish_id && count($this->res) !== 1) {
                    $wish->locking_team_id = 0;//解绑团体
                    $wish->status = 2;//放回心愿池
                    $_res[$wish->wish_id]->result = 2;//淘汰
                } else {
                    $wish->status = 5;//心愿状态转为 协商中
                    $_res[$wish->wish_id]->result = 1;//胜出
                }
                if (!$wish->save()) {throw new \Exception();}
                //发邮件通知见证人 和 心愿胜出者
                if ($wish->status == 5) {
                    //见证人
                    $mailer = Yii::$app->mailer->compose('wish_support',['name'=>$wish->getUsername('verify'),'role'=>'witness','wish_id'=>$wish->wish_id]);
                    $mailer->setFrom(Yii::$app->params['senderEmail']);
                    $mailer->setTo($wish->witness->email);
                    $mailer->setSubject('人恋人平台-心愿进展通知');
                    $mailer->send();
                    //受资助者
                    $mailer = Yii::$app->mailer->compose('wish_support',['name'=>$wish->getUsername('wish'),'role'=>'vip','wish_id'=>$wish->wish_id]);
                    $mailer->setFrom(Yii::$app->params['senderEmail']);
                    $mailer->setTo($wish->user->email);
                    $mailer->setSubject('人恋人平台-心愿进展通知');
                    $mailer->send();
                }
                if (!$_res[$wish->wish_id]->save()) {throw new \Exception();}//VoteRes::save()
            }
            $transaction->commit();//成功了无需提醒，直接结果界面
            Yii::$app->session->setFlash('statistics','结算成功，请耐心等待相关见证人联系您办理后续手续！');
        } catch (\Exception $e) {
            $transaction->rollback();
            var_dump($e);
            die();
            Yii::$app->session->setFlash('statistics','结算失败，可能是服务器繁忙，请稍后再试或反馈此问题');
            return false;
        }
        return true;
    }

    /**
     * 重启投票活动
     */
    public function reset()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->endtime = $this->endtime + 86400;
            if (!$this->save()) {throw new \Exception();}
            if (!UserVote::deleteAll(['vote_id'=>$this->vote_id])) {throw new \Exception();}
            foreach ($this->res as $key => $res) {
                $res->amount = 0;
                if (!$res->save()) {throw new \Exception();}
            }
            //重启投票发邮件告之
            //发给团体创建者
            $mailer = Yii::$app->mailer->compose('vote_reset',['email'=>$this->team->email,'team_name'=>$this->team->name,'vote_id'=>$this->vote_id]);
            $mailer->setFrom(Yii::$app->params['senderEmail']);
            $mailer->setTo($this->team->email);
            $mailer->setSubject('人恋人平台-投票活动重启');
            $mailer->send();
            Yii::$app->session->setFlash('reset','存在多个最低票候选者，已经为您重启投票且延长了一天自动结束时间');
            $transaction->commit();
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('reset','存在多个最低票候选者，重启尝试失败，请稍后再试或反馈此问题');
            $transaction->rollback();
        }
    }
}
