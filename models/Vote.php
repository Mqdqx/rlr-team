<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vote".
 *
 * @property int $vote_id 一次投票活动的主键ID
 * @property int $team_id 隶属团体的ID
 * @property string $title 此次投票的标题
 * @property int $support_num 最终资助的人数
 * @property int $candidate_num 候选人数
 * @property int $starttime 开始投票时间戳
 * @property int $endtime 自动结束时间戳
 * @property int $status 实时状态：1->投票中，2->投票已结束
 */
class Vote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id'], 'required'],
            [['team_id', 'support_num', 'candidate_num', 'starttime', 'endtime', 'status'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vote_id' => '一次投票活动的主键ID',
            'team_id' => '隶属团体的ID',
            'title' => '此次投票的标题',
            'support_num' => '最终资助的人数',
            'candidate_num' => '候选人数',
            'starttime' => '开始投票时间戳',
            'endtime' => '自动结束时间戳',
            'status' => '实时状态：1->投票中，2->投票已结束',
        ];
    }
}
