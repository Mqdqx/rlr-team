<?php

namespace app\models;

use Yii;
use app\models\Team;
use app\models\Message;

/**
 * This is the model class for table "team_message".
 *
 * @property int $id 主键ID
 * @property int $team_id 团体ID
 * @property int $message_id 信息ID
 */
class TeamMessage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'message_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_id' => 'Team ID',
            'message_id' => 'Message ID',
        ];
    }

    /**
     * 关联|Message
     */
    public function getMessage()
    {
        return $this->hasOne(Message::className(),['message_id'=>'message_id']);
    }

    /**
     * 关联|Team
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(),['team_id'=>'team_id']);
    }
}
