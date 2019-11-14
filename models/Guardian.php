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
 * @property string $bankcard 银行卡号
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
            [['truename','relation','bankcard','number'],'required','on'=>['updateGuardian']],
            ['truename','match','pattern'=>'/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','message'=>'过长或含有非法字符！','on'=>['updateGuardian']],
            ['bankcard','string','max'=>20,'on'=>['updateGuardian']],
            ['number','match','pattern'=>'/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/','message'=>'手机号码格式错误','on'=>['updateGuardian']],
            ['idcard','match','pattern'=>'/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/i','message'=>'身份证号码格式错误','on'=>['updateGuardian']],
            ['address','string','max'=>250,'on'=>['updateGuardian']],

            [['user_id', 'number'], 'integer'],
            [['truename', 'relation', 'idcard', 'address'], 'string', 'max' => 255],
            [['bankcard'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'guardian_id' => 'Guardian ID',
            'user_id' => 'User ID',
            'truename' => '真实姓名',
            'relation' => '监护关系',
            'idcard' => '身份证号',
            'number' => '手机号',
            'address' => '住址',
            'bankcard' => '银行卡号',
        ];
    }

    /**
     * (新建)更新监护人信息
     */
    public function updateGuardian($data)
    {
        if ($this->load($data) && $this->validate()) {
            $this->user_id = Yii::$app->user->identity->user_id;
            return (bool)$this->save();   
        }
        return false;
    }
}
