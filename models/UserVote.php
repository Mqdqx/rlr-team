<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_vote".
 *
 * @property int $id 主键ID
 * @property int $user_id 投票者user_id
 * @property int $vote_id 所参与此次投票活动的ID
 * @property int $vote_res_id 投给某个候选者的ID
 */
class UserTeam extends \yii\db\ActiveRecord
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
            [['user_id', 'vote_id', 'vote_res_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'vote_id' => 'Vote ID',
            'vote_res_id' => 'Vote Res ID',
        ];
    }
}
