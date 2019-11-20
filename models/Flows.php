<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\Team;
use app\models\Trade;

/**
 * This is the model class for table "flows".
 *
 * @property int $flows_id 流水主键ID
 * @property int $createtime 产生时间戳
 * @property string $out_role 出账方角色：vip/team/admin
 * @property int $out_id 出账方ID：个人id/团体id/平台
 * @property int $in_id 入账方ID：个人id/团体id/平台
 * @property string $in_role 入账方角色：vip/team/admin
 * @property string $money 金额，两位小数
 * @property int $type 流水类型：1->个人钱包充值，2->个人钱包提现，3->资助周期自动拨款，4->个人钱包至团体钱包，5->站内转账
 * @property int $endtime 完成时间戳
 * @property int $status 当前状态：0->已完成，1->提现申请待完成，2->充值交易未完成
 */
class Flows extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flows';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['money'],'required','message'=>'请输入金额','on'=>['recharge','rechargeTeam']],
            [['money'],'integer','message'=>'充值金额必须为整数','on'=>['recharge','rechargeTeam']],

            [['money'],'validateMoney','on'=>['rechargeTeam']],

            //体现申请规则
            ['money','required','message'=>'请输入金额','on'=>['withdraw']],
            ['money','integer','message'=>'提现金额必须为整数','on'=>['withdraw']],
            ['money','compare','compareValue'=>Yii::$app->user->identity->balance,'operator'=>'<=','message'=>'提现金额必须小于或等于当前余额','on'=>['withdraw']],

            [['createtime', 'out_id', 'in_id', 'type', 'endtime', 'status'], 'integer'],
            [['money'], 'number'],
            [['out_role', 'in_role'], 'string', 'max' => 30],
        ];
    }

    /**
     * 验证想为团体充值的用户个人余额是否不小于自己当前余额
     */
    public function validateMoney($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (Yii::$app->user->identity->balance < $this->money) {
                $this->addError($attribute, '个人余额不足!请先个人充值');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'flows_id' => '流水编号',
            'createtime' => '产生时间戳',
            'out_role' => '出账方角色：vip/team/admin',
            'out_id' => '出账方ID：个人id/团体id/平台',
            'in_id' => '入账方ID：个人id/团体id/平台',
            'in_role' => '入账方角色：vip/team/admin',
            'money' => '金额',
            'type' => '流水类型',
            'endtime' => '完成时间戳',
            'status' => '当前状态',
        ];
    }

    /**
     * 格式时间显示
     */
    public function getTime($timestamp)
    {
        if ($timestamp == 'createtime') {
            return date('y-m-d H:i:s',$this->createtime);
        } elseif ($timestamp == 'endtime') {
            return date('y-m-d H:i:s',$this->endtime);
        }
    }

    /**
     * 关联表|入账方|唯一的(vip/team)对象
     */
    public function getIn()
    {
        if ($this->in_role == 'vipPurse') {
            return $this->hasOne(User::className(),['user_id'=>'in_id']);
        } elseif ($this->in_role == 'teamPurse') {
            return $this->hasOne(Team::className(),['team_id'=>'in_id']);
        }
    }

    /**
     * 关联表|出账方|唯一的(vip/team)对象
     */
    public function getOut()
    {
        if ($this->out_role == 'vipPurse') {
            return $this->hasOne(User::className(),['user_id'=>'out_id']);
        } elseif ($this->out_role == 'teamPurse') {
            return $this->hasOne(Team::className(),['team_id'=>'out_id']);
        }
    }

    /**
     * 获取出账/入账方 显示时的 名称
     */
    public function get_name($role='in')
    {
        if ($role == 'in') {
            if ($this->in_role == 'vipPurse') {
                return $this->in->username ? $this->in->username : $this->in->email;
            } elseif ($this->in_role == 'teamPurse') {
                return $this->in->name;
            } elseif ($this->in_role == 'vipBank') {
                return '银行卡';
            }
        } elseif ($role == 'out') {
            if ($this->out_role == 'vipPurse') {
                return $this->out->username ? $this->out->username : $this->out->email;
            } elseif ($this->out_role == 'teamPurse') {
                return $this->out->name;
            } elseif ($this->out_role == 'vipAlipay') {
                return '支付宝';
            }
        }
    }

    /**
     * 格式化 流水类型 用于显示
     */
    public function type()
    {
        switch ($this->type) {
            case '1':
                $type = '钱包充值';
                break;
            case '2':
                $type = '钱包提现';
                break;
            case '3':
                $type = '自动转账'; 
                break;
            case '4':
                $type = '团体充值';
                break;
            default:
                $type = '未知错误';
                break;
        }
        return $type;
    }

    /**
     * 格式化 流水状态 用于显示
     */
    public function status()
    {
        switch ($this->status) {
            case '0':
                $status = '已完成';
                break;
            case '1':
                $status = '转账中';
                break;
            case '2':
                $status = '交易未完成';
                break;
            default:
                $status = '未知错误';
                break;
        }
        return $status;
    }

    public function color()
    {
        $color = '';
        if (Yii::$app->request->get('r') == 'vip/finance') {
            if (Yii::$app->user->identity->user_id == $this->in_id) {
                $color = 'success';
            } elseif (Yii::$app->user->identity->user_id == $this->out_id) {
                $color = 'warning';
            }
        }
        if (Yii::$app->request->get('r') == 'team/finance') {
            if (Yii::$app->session->get('team')->team_id == $this->in_id) {
                $color = 'success';
            } elseif (Yii::$app->session->get('team')->team_id == $this->out_id) {
                $color = 'warning';
            }
        }
        return $color;
    }

    /**
     * 传参 心愿对象
     * 只许返回真假
     */
    public function wishTransfer($wish)
    {
        $this->createtime = time();
        $this->out_role = $wish->sponsor->role . 'Purse';
        $this->out_id = $wish->sponsor->id;
        $this->in_role = 'vipPurse';
        $this->in_id = $wish->user_id;
        $this->money = $wish->per;
        $this->type = 3;
        $this->endtime = time();
        $this->status = 0;

        return (bool)$this->save();
    }

    /**
     * 用户体现申请
     */
    public function withdraw($data)
    {
        $this->scenario = 'withdraw';
        if ($this->load($data) && $this->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                Yii::$app->user->identity->balance -= $this->money;
                if (!Yii::$app->user->identity->save()) {throw new \Exception();}
                $this->createtime = time();
                $this->out_role = 'vipPurse';
                $this->out_id = Yii::$app->user->identity->user_id;
                $this->in_id = Yii::$app->user->identity->user_id;
                $this->in_role = 'vipBank';
                $this->type = 2;
                $this->status = 1;
                if (!$this->save()) {throw new \Exception();}
                $transaction->commit();
                return true;
            } catch (\Exception $e) {
                $transaction->rollback();
                return false;
            }
        }
        return false;
    }

    /**
     * vip|用户充值，接入支付宝SDK前做的准备
     */
    public function rechargeAlipay($data)
    {
        if ($this->load($data) && $this->validate()) {
            $this->createtime = time();
            $this->out_role = 'vipAlipay';
            $this->out_id = Yii::$app->user->identity->user_id;
            $this->in_id = Yii::$app->user->identity->user_id;
            $this->in_role = 'vipPurse';
            $this->type = 1;
            $this->status = 2;//交易未完成
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$this->save()) {
                    throw new \Exception();
                }
                $trade = new Trade();
                $trade->flows_id = $this->flows_id;
                $trade->money = $this->money;
                $trade->out_trade_no = intval(date('YmdHis').mt_rand(100,999));
                $trade->type = 0;
                $trade->status = 0;
                if (!$trade->save()) {
                    throw new \Exception();
                }
                $transaction->commit();
                return $trade;
            } catch (\Exception $e) {
                $transaction->rollback();
                return false;
            }
        }
        return false;
    }

    /**
     * vip|用户为一个团体充值
     */
    public function rechargeTeam($data)
    {
        if ($this->load($data) && $this->validate()) {
            $this->createtime = time();
            $this->out_id = Yii::$app->user->identity->user_id;
            $this->out_role = 'vipPurse';
            $this->in_role = 'teamPurse';
            $this->in_id = Yii::$app->session->get('team')->team_id;
            $this->type = 4;
            $this->status = 0;
            $this->endtime = time();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$this->save()) {
                    throw new \Exception();
                }
                Yii::$app->user->identity->balance -= $this->money;
                if (!Yii::$app->user->identity->save()) {
                    throw new \Exception();
                }
                Yii::$app->session->get('team')->balance += $this->money;
                if (!Yii::$app->session->get('team')->save()) {
                    throw new \Exception();
                }
                $transaction->commit();
                return true;
            } catch (\Exception $e) {
                $transaction->rollback();
                return false;
            }
        }
        return false;
    }
}
