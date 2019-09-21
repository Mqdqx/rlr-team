<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_vote".
 *
 * @property int $user_id 投票者user_id
 * @property int $vote_id 所参与此次投票活动的ID
 * @property int $vote_res_id 投给某个候选者的ID
 */
class UserVote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_vote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'vote_id', 'vote_res_id'], 'integer'],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '投票者user_id',
            'vote_id' => '所参与此次投票活动的ID',
            'vote_res_id' => '投给某个候选者的ID',
        ];
    }
}
