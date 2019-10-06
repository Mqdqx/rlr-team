<?php

namespace app\models;

use Yii;
use app\models\User;

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
 * @property int $status 当前状态：0->已完成，1->提现申请待完成
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
            [['createtime', 'out_id', 'in_id', 'type', 'endtime', 'status'], 'integer'],
            [['money'], 'number'],
            [['out_role', 'in_role'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'flows_id' => '流水主键ID',
            'createtime' => '产生时间戳',
            'out_role' => '出账方角色：vip/team/admin',
            'out_id' => '出账方ID：个人id/团体id/平台',
            'in_id' => '入账方ID：个人id/团体id/平台',
            'in_role' => '入账方角色：vip/team/admin',
            'money' => '金额，两位小数',
            'type' => '流水类型',
            'endtime' => '完成时间戳',
            'status' => '当前状态：0->已完成，1->提现申请待完成',
        ];
    }

    /**
     * 传参 心愿对象
     * 只许返回真假
     */
    public function wishTransfer($wish)
    {
        $this->createtime = time();
        $this->out_role = $wish->sponsor . 'Purse';
        $this->out_id = $wish->sponsor->id;
        $this->in_role = 'vipPurse';
        $this->in_id = $wish->user_id;
        $this->money = $wish->per;
        $this->type = 3;
        $this->endtime = time();
        $this->status = 0;

        return (bool)$this->save();
    }
}
