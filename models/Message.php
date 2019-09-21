<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "message".
 *
 * @property int $message_id 站内信主键ID
 * @property int $sendtime 发送时间戳
 * @property int $from 发送者user_id
 * @property int $to 接受者user
 * @property string $title 标题
 * @property string $content 正文内容
 * @property int $status 状态：1->发送成功，2->已读
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sendtime', 'from', 'to', 'status'], 'integer'],
            [['to'], 'required'],
            [['title', 'content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'message_id' => '站内信主键ID',
            'sendtime' => '发送时间戳',
            'from' => '发送者user_id',
            'to' => '接受者user',
            'title' => '标题',
            'content' => '正文内容',
            'status' => '状态：1->发送成功，2->已读',
        ];
    }
}
