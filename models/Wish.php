<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wish".
 *
 * @property int $wish_id 心愿主键ID
 * @property int $user_id 所属用户的user_id
 * @property int $createtime 创建时间戳
 * @property string $money 期望金额，两位小数
 * @property int $month 资助周期，单位：月
 * @property int $label 标签：0->无，1->灾祸，2->单亲，3->孤儿and so on....
 * @property int $range 可见范围社区：0->隶属社区，1->所以社区
 * @property string $file 上传补充文件路径
 * @property string $description 描述/原因
 * @property int $verify 是否审核/审核结果，0->未被审核，1->审核通过，2-审核拒绝
 * @property string $verify_res 审核批注
 * @property int $verify_user_id 审核员/见证人/社区管理员(witness)的user_id
 * @property int $verify_time 审核时间戳
 * @property int $status 审核完状态：
 * 0->对应心愿池中，1->资助人锁定待线下协商中，2->资助人协商完成进入资助周期了，5->团体锁定待协商中，6->团体协商完成进入资助周期了，7->团体选取投票中，10->资助完成
 * @property int $locking_id 锁定的对象id：资助人id/团体id
 * @property int $vision 版本号（乐观锁）
 */
class Wish extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wish';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'createtime', 'month', 'label', 'range', 'verify', 'verify_user_id', 'verify_time', 'status', 'locking_id', 'vision'], 'integer'],
            [['money'], 'number'],
            [['verify'], 'required'],
            [['file'], 'string', 'max' => 100],
            [['description', 'verify_res'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'wish_id' => '心愿主键ID',
            'user_id' => '所属用户的user_id',
            'createtime' => '创建时间戳',
            'money' => '期望金额，两位小数',
            'month' => '资助周期，单位：月',
            'label' => '标签：0->无，1->灾祸，2->单亲，3->孤儿and so on....',
            'range' => '可见范围社区：0->隶属社区，1->所以社区',
            'file' => '上传补充文件路径',
            'description' => '描述/原因',
            'verify' => '是否审核/审核结果，0->未被审核，1->审核通过，2-审核拒绝',
            'verify_res' => '审核批注',
            'verify_user_id' => '审核员/见证人/社区管理员(witness)的user_id',
            'verify_time' => '审核时间戳',
            'status' => '
                审核完状态：
                0->对应心愿池中，
                1->资助人锁定待线下协商中，
                2->资助人协商完成进入资助周期了，
                5->团体锁定待协商中，
                6->团体协商完成进入资助周期了，
                7->团体选取投票中，
                10->资助完成
            ',
            'locking_id' => '锁定的对象id：资助人id/团体id',
            'vision' => '版本号（乐观锁）',
        ];
    }
}
