<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "team".
 *
 * @property int $team_id 团体主键ID
 * @property int $createtime 团体创建时间戳
 * @property int $user_id 创建者的user_id
 * @property int $status 团体状态：0->冻结，1->正常运行状态，2->创建审核中状态
 * @property string $balance 团体实时余额，两位小数
 * @property string $name 团体名称：班级名称
 * @property int $community_id 隶属社区id
 */
class Team extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['createtime', 'user_id', 'status', 'community_id'], 'integer'],
            [['balance'], 'number'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'team_id' => '团体主键ID',
            'createtime' => '团体创建时间戳',
            'user_id' => '创建者的user_id',
            'status' => '团体状态：0->冻结，1->正常运行状态，2->创建审核中状态',
            'balance' => '团体实时余额，两位小数',
            'name' => '团体名称：班级名称',
            'community_id' => '隶属社区id',
        ];
    }

    /**
     * 返回team_ID
     */
    public function getId()
    {
        return $this->team_id;
    }

    /**
     * 返回 ___邮箱地址
     */
    public function getEmail()
    {
        return 'classMaster@rlr.com';
    }

    public function getRole()
    {
        return 'team';
    }
}
