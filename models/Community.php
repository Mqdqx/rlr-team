<?php

namespace app\models;

use Yii;
use app\models\User;

/**
 * This is the model class for table "community".
 *
 * @property int $community_id 社区主键ID
 * @property int $createtime 社区创建时间戳
 * @property int $user_id 关联的主见证人账号ID
 * @property int $minpercent 资助者资助时对应心愿的最小余额比
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
            [['community_id','community_name','user_id'],'required','on'=>['newone']],
            ['community_id','integer','on'=>['newone']],
            [['community_id'], 'unique','on'=>['newone']],
            [['community_name'], 'unique','on'=>['newone']],
            ['minpercent','integer','on'=>['newone']],
            ['minpercent', 'compare', 'compareValue' => 100, 'operator' => '<=', 'on'=>['newone']],
            
            //save 或 update 规则，gii生成定义的
            [['community_id'], 'required'],
            [['community_id', 'createtime', 'user_id', 'status', 'minpercent'], 'integer'],
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
            'community_id' => '社区编号',
            'createtime' => '社区创建时间戳',
            'user_id' => '关联的主见证人账号',
            'community_name' => '社区名称：学院/学校',
            'remarks' => '社区备注/介绍/描述',
            'status' => '状态：1->正常使用，0->冻结状态',
        ];
    }

    /**
     * 新建社区
     */
    public function newone($data)
    {
        $this->scenario = "newone";
        if ($this->load($data) && $this->validate()) {
            $this->createtime = time();
            $user = User::findOne(['user_id'=>$this->user_id]);
            $user->status = 1;
            if ($this->save() && $user->save()) {
                return true;
            } else {
                throw new \Exception();
            }
        }
        return false;
    }
}
