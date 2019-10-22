<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vote_res".
 *
 * @property int $id 投票任何时刻结果主键ID
 * @property int $vote_id 隶属的投票活动的ID
 * @property int $wish_id 候选者的心愿ID
 * @property int $amount 获得票数
 * @property int $result 结果：0->投票未结束，1->胜出，2->淘汰
 * @property int $version 版本号(乐观锁)
 */
class VoteRes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vote_res';
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
            [['vote_id', 'wish_id', 'amount', 'result', 'version'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vote_id' => 'Vote ID',
            'wish_id' => 'Wish ID',
            'amount' => 'Amount',
            'result' => 'Result',
            'version' => 'Version',
        ];
    }
}
