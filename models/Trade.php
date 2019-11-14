<?php

namespace app\models;

use Yii;
use app\models\Flows;

/**
 * This is the model class for table "trade".
 *
 * @property int $id 主键ID
 * @property string $trade_no 支付宝反还的交易单号
 * @property string $out_trade_no 平台此次交易号
 * @property int $flows_id 关联的流水单号
 * @property int $type 交易类型：0->付款，1->退款
 * @property int $status 当前状态:0->待异步验证，1->异步/验证成功，2->异步/验证失败
 * @property string $money 金额大小
 */
class Trade extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trade';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['out_trade_no', 'flows_id', 'type', 'status'], 'integer'],
            [['trade_no'], 'string', 'max' => 30],
            [['out_trade_no','trade_no'],'unique'],
            [['money'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trade_no' => 'Trade No',
            'out_trade_no' => 'Out Trade No',
            'flows_id' => 'Flows ID',
            'type' => 'Type',
            'status' => 'Status',
            'money' => 'Money',
        ];
    }

    /**
     * 关联对应唯一的flows表
     */
    public function getFlows()
    {
        return $this->hasOne(Flows::className(),['flows_id'=>'flows_id']);
    }
}
