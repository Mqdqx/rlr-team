<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jointeam".
 *
 * @property int $jointeam_id 加入团体的邀请/申请
 * @property int $sendtime 发生时间戳
 * @property int $from 发起者的user_id
 * @property int $to 接受者的user_id
 * @property int $team_id 关联的团体id
 * @property string $message 发送者附上的留言
 * @property int $status 实时状态：1->待回复，2->同意，3->拒接
 */
class Jointeam extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jointeam';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sendtime', 'from', 'to', 'team_id', 'status'], 'integer'],
            [['message'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jointeam_id' => '加入团体的邀请/申请',
            'sendtime' => '发生时间戳',
            'from' => '发起者的user_id',
            'to' => '接受者的user_id',
            'team_id' => '关联的团体id',
            'message' => '发送者附上的留言',
            'status' => '实时状态：1->待回复，2->同意，3->拒接',
        ];
    }
}
