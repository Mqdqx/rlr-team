<?php

namespace app\models;

use Yii;
use app\models\User;
use yiier\region\models\Region;

/**
 * This is the model class for table "community".
 *
 * @property int $community_id 社区主键ID
 * @property int $createtime 社区创建时间戳
 * @property int $province_id
 * @property int $city_id
 * @property int $user_id 关联的主见证人账号ID
 * @property int $minpercent 资助者资助时对应心愿的最小余额比
 * @property string $address 具体街道地址
 * @property string $community_name 社区名称：学院/学校
 * @property string $remarks 社区备注/介绍/描述
 * @property int $status 状态：1->正常使用，0->冻结状态，4->待审核，5->暂存份
 */
class Community extends \yii\db\ActiveRecord
{
    const NORMAL = 1;
    const WATING = 4;
    public static $status = [
        self::NORMAL => '正常',
        self::WATING => '待审核',
    ];
    /**
     * 一些临时量
     */
    public $truename;
    public $number;
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
            [['community_name','province_id','city_id','address','truename','number','minpercent'],'required','on'=>['newone']],
            [['community_name'], 'unique','on'=>['newone']],
            [['community_name'], 'string','max'=>20,'on'=>['newone']],
            ['truename','match','pattern'=>'/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','message'=>'过长或含有非法字符！','on'=>['newone']],
            ['number','match','pattern'=>'/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/','message'=>'手机号码格式错误','on'=>['newone']],
            [['address'], 'string','max'=>50,'on'=>['newone']],
            ['minpercent','integer','on'=>['newone']],
            ['minpercent', 'compare', 'compareValue' => 1, 'operator' => '>=', 'on'=>['newone']],
            ['minpercent', 'compare', 'compareValue' => 100, 'operator' => '<=', 'on'=>['newone']],
            
            //save 或 update 规则，gii生成定义的
            [['community_id', 'createtime', 'user_id', 'status', 'minpercent','province_id','city_id'], 'integer'],
            [['community_name', 'remarks','address'], 'string', 'max' => 255],
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
            'minpercent' => '最小余额比',
            'province_id' => '省',
            'city_id' => '市',
            'user_id' => '见证人账号',
            'address' => '具体街道地址',
            'truename' => '真实姓名',
            'number' => '手机号码',
            'community_name' => '社区名称',
            'remarks' => '社区备注/介绍/描述',
            'status' => '当前状态',
        ];
    }

    /**
     * 关联 唯一见证人
     */
    public function getUser()
    {
        return $this->hasOne(User::className(),['user_id'=>'user_id']);
    }

    /**
     * 取出社区所属地
     */
    public function getRegion()
    {
        $province = Region::findOne(['id'=>$this->province_id])->name;
        $city = Region::findOne(['id'=>$this->city_id])->name;
        return $province.' '.$city;
    }

    /**
     * 见证人提供社区信息
     */
    public function newone($data)
    {
        if ($this->load($data) && $this->validate()) {
            $this->createtime = time();
            $this->status = 4;
            $this->user_id = Yii::$app->user->identity->user_id;
            Yii::$app->user->identity->truename = $this->truename;
            Yii::$app->user->identity->username = $this->community_name;
            Yii::$app->user->identity->number = $this->number;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$this->save()) {
                    throw new \Exception();
                }
                if (!Yii::$app->user->identity->save()) {
                    throw new \Exception();
                }
                $transaction->commit();
                return true;
            } catch (\Exception $e) {
                $transaction->rollback();
                return false;
            }
        }
        return false;
    }

    /**
     * 超级管理员审核一个社区
     */
    public function approve($data)
    {
        if ($this->load($data) && $this->validate()) {
            $this->status = 1;
            $user = User::findOne(['user_id'=>$this->user_id]);
            $user->status = 1;
            $user->truename = $this->truename;
            $user->number = $this->number;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$this->save()) {
                    throw new \Exception();
                }
                if (!$user->save()) {
                    throw new \Exception();
                }
                $transaction->commit();
                $mailer = Yii::$app->mailer->compose('community_approved',['truename'=>$this->truename,'community_name'=>$this->community_name]);
                $mailer->setFrom(Yii::$app->params['senderEmail']);
                $mailer->setTo($user->email);
                $mailer->setSubject("人恋人平台-审核成功通知");
                $mailer->send();
                return true;
            } catch (\Exception $e) {
                $transaction->rollback();
                return false;
            }
        }
        return false;
    }

}
