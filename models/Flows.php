<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "flows".
 *
 * @property int $flows_id 流水主键ID
 * @property int $createtime 产生时间戳
 * @property int $out_id 出账方ID：个人id/团体id/平台
 * @property int $in_id 入账方ID：个人id/团体id/平台
 * @property string $money 金额，两位小数
 * @property int $category 流水类型：1->个人账户充值，2->体现，3->资助周期自动拨款
 * @property int $endtime 完成时间戳，0->表示人工位尚未核对/操作
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
            [['createtime', 'out_id', 'in_id', 'category', 'endtime'], 'integer'],
            [['money'], 'number'],
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
            'out_id' => '出账方ID：个人id/团体id/平台',
            'in_id' => '入账方ID：个人id/团体id/平台',
            'money' => '金额，两位小数',
            'category' => '流水类型：1->个人账户充值，2->体现，3->资助周期自动拨款',
            'endtime' => '完成时间戳，0->表示人工位尚未核对/操作',
        ];
    }
}
