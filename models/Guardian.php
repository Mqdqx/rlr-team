<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "guardian".
 *
 * @property int $guardian_id 监护人信息主键ID
 * @property int $user_id 关联学生user_id
 * @property string $truename 监护人真实姓名
 * @property string $relation 学生与监护人关系
 * @property string $idcard 监护人身份证号码
 * @property string $number 监护人手机号码
 * @property string $address 监护人常居住地址
 */
class Guardian extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guardian';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'number'], 'integer'],
            [['truename', 'relation', 'idcard', 'address'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'guardian_id' => '监护人信息主键ID',
            'user_id' => '关联学生user_id',
            'truename' => '监护人真实姓名',
            'relation' => '学生与监护人关系',
            'idcard' => '监护人身份证号码',
            'number' => '监护人手机号码',
            'address' => '监护人常居住地址',
        ];
    }
}
