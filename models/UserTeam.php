<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_team".
 *
 * @property int $id 关联表主键ID
 * @property int $user_id 用户ID
 * @property int $team_id 团体ID
 */
class UserTeam extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'team_id'], 'integer'],
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
            'team_id' => 'Team ID',
        ];
    }

    /**
     * 验证某位用户是否为一个团体的成员
     */
    public static function isMember($user_id,$team_id)
    {
        return self::findOne(['user_id'=>$user_id,'team_id'=>$team_id]);
    }
}
