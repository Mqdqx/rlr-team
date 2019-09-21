<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_team".
 *
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
            [['user_id', 'team_id'], 'required'],
            [['user_id', 'team_id'], 'integer'],
            [['user_id', 'team_id'], 'unique', 'targetAttribute' => ['user_id', 'team_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'team_id' => '团体ID',
        ];
    }
}
