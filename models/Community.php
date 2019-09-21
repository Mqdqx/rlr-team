<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "community".
 *
 * @property int $community_id 社区主键ID
 * @property int $createtime 社区创建时间戳
 * @property int $user_id 关联的主见证人账号ID
 * @property string $community_name 社区名称：学院/学校
 * @property string $remarks 社区备注/介绍/描述
 * @property int $status 状态：1->正常使用，0->冻结状态
 */
class Community extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'community';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['community_id'], 'required'],
            [['community_id', 'createtime', 'user_id', 'status'], 'integer'],
            [['community_name', 'remarks'], 'string', 'max' => 255],
            [['community_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'community_id' => '社区主键ID',
            'createtime' => '社区创建时间戳',
            'user_id' => '关联的主见证人账号ID',
            'community_name' => '社区名称：学院/学校',
            'remarks' => '社区备注/介绍/描述',
            'status' => '状态：1->正常使用，0->冻结状态',
        ];
    }
}
