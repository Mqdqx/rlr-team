<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wish_flows".
 *
 * @property int $id 心愿-流水关联表主键
 * @property int $wish_id 心愿wish_id
 * @property int $flows_id 流水flows_id
 */
class WishFlows extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wish_flows';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wish_id'], 'required'],
            [['wish_id', 'flows_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '心愿-流水关联表主键',
            'wish_id' => '心愿wish_id',
            'flows_id' => '流水flows_id',
        ];
    }
}
