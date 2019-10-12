<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\UserTeam;
use app\models\Team;
use app\models\TeamMessage;

/**
 * This is the model class for table "message".
 *
 * @property int $message_id 站内信主键ID
 * @property int $sendtime 发送时间戳
 * @property int $from 发送者user_id
 * @property int $to 接受者user_id
 * @property string $title 标题
 * @property string $content 正文内容
 * @property int $type 信息类型:0->普通信息，1->系统通知，2->团体加入邀请，3->团体通知，4->社区通知，
 * @property int $status 状态：0->待处理,1->未读，2->已读，3->同意加入，4->拒绝加入
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * 临时量
     */
    public $receiver;
    public $_toUser;//为一个User对象

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receiver','title','content'],'required','on'=>['send']],
            ['title','string','max'=>30,'on'=>['send']],
            ['content','string','max'=>255,'on'=>['send']],

            ['receiver','required','on'=>['joinTeam']],

            ['receiver','validateReceiver','on'=>['send','joinTeam']],

            ['receiver','validateIsWitness','on'=>['joinTeam']],
            ['receiver','validateInvited','on'=>['joinTeam']],

            //默认规则
            [['sendtime', 'from', 'to', 'type' ,'status'], 'integer'],
            [['title', 'content'], 'string', 'max' => 255],
        ];
    }

    /**
     * 后端验证收件人是否存在,若不存在，则发送邮件邀请入驻平台
     */
    public function validateReceiver($attribute, $params)
    {
        if (!$this->hasErrors()) {
            //用户输入值是否为邮箱格式
            if(filter_var($this->receiver, FILTER_VALIDATE_EMAIL)) {
                $this->_toUser = User::findOne(['email'=>$this->receiver]);
            } else {
                $this->_toUser = User::findOne(['username'=>$this->receiver]);
            }
            if (!$this->_toUser) {
                if (filter_var($this->receiver, FILTER_VALIDATE_EMAIL)) {
                    $newUser = new User();
                    $newUser->email = $this->receiver;
                    $newUser->createtime = time();
                    $newUser->token = $newUser->createToken();
                    $newUser->status = 5;
                    $newUser->role = 'vip';
                    $inviter = Yii::$app->user->identity->truename ? Yii::$app->user->identity->truename : Yii::$app->user->identity->username;
                    $mailer = Yii::$app->mailer->compose('invite',['email'=>$this->receiver,'token'=>$newUser->token,'inviter'=>$inviter]);
                    $mailer->setFrom(Yii::$app->params['senderEmail']);
                    $mailer->setTo($this->receiver);
                    $mailer->setSubject('人恋人平台-邀请注册');
                    $result = $newUser->save() && $mailer->send();
                    $this->_toUser = $newUser;//用于这个验证器验证成功后
                    if (!$result) {
                        $this->addError($attribute, '发送失败，请稍后重试或反馈此问题!');
                    }
                } else {
                    $this->addError($attribute, '所填用户不存在且非邮箱地址!');
                }
            }
        }
    }

    /**
     * 验证是否已经邀请过该成员
     */
    public function validateInvited($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $invited = false;
            $mails = self::find()->where(['to'=>$this->_toUser->user_id,'type'=>2])->all();
            foreach ($mails as $key => $mail) {
                $invited = ($mail->team->team_id == Yii::$app->session->get('team')->team_id) ? true : $invited;
            }
            if ($invited) {
                $this->addError($attribute, '团体已经邀请过此用户了！');
            }
        }
    }

    /**
     * 验证收件人角色是否为见证人witness
     */
    public function validateIsWitness($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->_toUser->role == 'witness' || $this->_toUser->role == 'admin') {
                $this->addError($attribute, '见证人不能加人团体！');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'receiver' => '收件人',
            'message_id' => '站内信主键ID',
            'sendtime' => '发送时间戳',
            'from' => '发送者user_id',
            'to' => '接受者user_id',
            'title' => '标题',
            'content' => '正文内容',
            'type' => '信息类型:0->普通信息，1->系统通知，2->团体加入邀请，3->团体通知，4->社区通知',
            'status' => '状态：1->未读，2->已读',
        ];
    }

    /**
     * 关连表|(from)User
     */
    public function getFromUser()
    {
        return $this->hasOne(User::className(),['user_id'=>'from']);
    }

    /**
     * 关连表|(to)User
     */
    public function getToUser()
    {
        return $this->hasOne(User::className(),['user_id'=>'to']);
    }

    /**
     * 关联表|Team
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(),['team_id'=>'team_id'])->viaTable('team_message',['message_id'=>'message_id']);
    }

    /**
     * 格式化发送时间
     */
    public function sendtime()
    {
        return date('y-m-d H:i:s',$this->sendtime);
    }

    /**
     * 格式化信息类型
     */
    public function type()
    {
        switch ($this->type) {
            case '0':
                $type = '普通信息';
                break;
            case '2':
                $type = '团体邀请';
                break;
            default:
                $type = '未知错误';
                break;
        }
        return $type;
    }

    /**
     * 格式化当前状态
     */
    public function status()
    {
        switch ($this->status) {
            case '0':
                $status = '待处理';
                break;
            case '1':
                $status = '未读';
                break;
            case '2':
                $status = '已读';
                break;
            case '3':
                $status = '同意加入';
                break;
            case '4':
                $status = '拒绝加入';
                break;
            default:
                $status = '未知错误';
                break;
        }
        return $status;
    }

    /**
     * 格式化表格行显示颜色
     */
    public function color()
    {
        switch ($this->status) {
            case '0':
                $color = 'warning';
                break;
            case '1':
                $color = 'info';
                break;
            case '2':
                $color = 'active';
                break;
            case '3':
                $color = 'active';
                break;
            case '4':
                $color = 'active';
                break;
            default:
                $color = '';
                break;
        }
        return $color;
    }

    /**
     * vip | 发送普通类型的信息
     */
    public function send($data)
    {
        if ($this->load($data) && $this->validate()) {
            $this->sendtime = time();
            $this->from = Yii::$app->user->identity->user_id;
            $this->to = $this->_toUser->user_id;
            $this->type = 0;
            $this->status = 1;
            return (bool)$this->save();
        }
        return false;
    }

    /**
     * 发送邀请加入团体的消息
     */
    public function joinTeam($data)
    {
        if ($this->load($data) && $this->validate()) {
            $this->sendtime = time();
            $this->title = '团体加入邀请';
            $this->content = Yii::$app->user->identity->username.'  用户邀请您加入 '.Yii::$app->session->get('team')->name.' 团体！';
            $this->type = 2;
            $this->status = 0;
            $this->from = Yii::$app->user->identity->user_id;
            $this->to = $this->_toUser->user_id;
            if (!$this->save()) {
                throw new \Exception();
            }
            $TeamMessage = new TeamMessage();
            $TeamMessage->message_id = $this->message_id;
            $TeamMessage->team_id = Yii::$app->session->get('team')->team_id;
            if (!$TeamMessage->save()) {
                throw new \Exception();
            }
            $mailer = Yii::$app->mailer->compose('jointeam',['team_message_id'=>$TeamMessage->id,'email'=>$this->_toUser->email,'inviter'=>Yii::$app->user->identity->username,'teamanme'=>Yii::$app->session->get('team')->name]);
            $mailer->setFrom(Yii::$app->params['senderEmail']);
            $mailer->setTo($this->_toUser->email);
            $mailer->setSubject("人恋人平台-团体加入邀请");
            if (!$mailer->send()) {
                throw new \Exception();
            }
            return true;
        }
        return false;
    }

    /**
     * 收件人 处理一条 需要 选择决定 的信息
     */
    public function decide($decision)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->status = ($decision == 'agree') ? 3 : 4;
            if (!$this->save()) {
                throw new \Exception;
            }
            if ($this->status == 3) {
                $userTeam = new UserTeam();
                $userTeam->user_id = $this->to;
                $userTeam->team_id = $this->team->team_id;
                if (!$userTeam->save()) {
                    throw new \Exception;
                }
            }
            $transaction->commit();
            $res = true;
        } catch (\Exception $e) {
            $transaction->rollback();
            $res = false;
        }
        return $res;
    }

}
