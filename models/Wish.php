<?php

namespace app\models;

use Yii;
use app\models\User;

/**
 * This is the model class for table "wish".
 *
 * @property int $wish_id 心愿主键ID
 * @property int $user_id 所属用户的user_id
 * @property int $tokentime 产生心愿码时间戳
 * @property string $token 见证人产生心愿码
 * @property string $money 期望金额，两位小数
 * @property int $month 资助周期，单位：月
 * @property int $transfered 已经转账的期数，单位：月
 * @property int $label 标签：0->无，1->灾祸，2->单亲，3->孤儿and so on....
 * @property int $range 可见范围
 * @property string $file 上传补充文件路径
 * @property string $description 描述/原因
 * @property string $verify_res 审核批注
 * @property int $verify_user_id 审核员/见证人/社区管理员(witness)的user_id
 * @property int $verify_time 审核时间戳
 * @property int $start_time 启动时间戳
 * @property int $status 0->心愿码待激活，1->待对应见证人审核，2->审核通过在心愿池待资助者资助，3->资助者绑定待线下协商，4->资助人协商完成进入资助周期了，
 * 5->团体锁定待线下协商中，6->团体协商完成进入资助周期了，7->团体选取投票中，9-审核驳回，10->资助完成
 * @property int $locking_id 锁定的对象id：资助人id/团体id
 * @property int $version 版本号（乐观锁）
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
     * 乐观锁
     * @return string
     */
    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'tokentime', 'month','transfered' ,'label', 'range', 'verify_user_id', 'verify_time','start_time', 'status', 'locking_id', 'vision'], 'integer'],
            [['money'], 'number'],
            [['file','token'], 'string', 'max' => 100],
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
            'token' => '见证人产生心愿码',
            'tokentime' => '产生心愿码时间戳',
            'money' => '期望金额，两位小数',
            'month' => '资助周期，单位：月',
            'label' => '标签：0->其它，1->灾祸，2->单亲，3->孤儿and so on....',
            'range' => '可见范围',
            'file' => '上传补充文件路径',
            'description' => '描述/原因',
            'verify_res' => '审核批注',
            'verify_user_id' => '审核员/见证人/社区管理员(witness)的user_id',
            'verify_time' => '审核时间戳',
            'status' => '当前状态',
            'locking_id' => '锁定的对象id：资助人id/团体id',
            'vision' => '版本号（乐观锁）',
        ];
    }

    /**
     * 转化各个时间的格式用于显示
     */
    public function getTime($stamp) {
        return date('y-m-d H:i:s',$stamp);
    }

    /**
     * 转译心愿当前状态用于前台表格行的颜色
     */
    public function color()
    {
        switch ($this->status) {
            case '0':
                $color = 'info';
                break;
            case '1':
                $color = 'success';
                break;
            default:
                $color = '';
                break;
        }
        return $color;
    }

    /**
     * 转移心愿当前状态用于显示
     */
    public function status()
    {
        switch ($this->status) {
            case '0':
                $status = '未激活';
                break;
            case '1':
                $status = '待审核';
                break;
            default:
                $status = '未知错误';
                break;
        }
        return $status;
    }

    /**
     * 通过user_id获取被资助者的姓名
     * 或者返回未激活
     */
    public function getUsername()
    {
        $user = User::findOne(['user_id'=>$this->user_id]);
        if ($user) {
            return $user->username;
        }
        return '未激活';
    }

    /**
     * 产生一个心愿码(心愿),同时记录时间戳和捆绑verify_user_id
     */
    public function generateToken()
    {
        $this->status = 0;
        $this->verify_user_id = Yii::$app->user->identity->user_id;
        $this->tokentime = time();
        $this->token = md5($this->verify_user_id.$this->tokentime);
        return (bool)$this->save();
    }

    /**
     * 响应witness管理心愿时的查询(分类查询)
     * 静态方法
     */
    public static function find_Witness($category)
    {
        switch ($category) {
            case 'noactivate':
                return static::find()->where(['status'=>'0','verify_user_id'=>Yii::$app->user->identity->user_id])->orderBy(['tokentime'=>SORT_DESC]);
                break;
            
            default:
                return null;
                break;
        }
    }
}
