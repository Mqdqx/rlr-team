<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\UserTeam;

/**
 * This is the model class for table "team".
 *
 * @property int $team_id 团体主键ID
 * @property int $createtime 团体创建时间戳
 * @property int $user_id 创建者的user_id
 * @property int $status 团体状态：0->冻结，1->正常运行状态
 * @property string $balance 团体实时余额，两位小数
 * @property string $name 团体名称：班级名称
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
            [['name'],'required','on'=>['newone']],
            [['name'],'string','max'=> 30 ,'on'=>['newone']],
            [['name'],'unique','on'=>['newone']],

            [['createtime', 'user_id', 'status'], 'integer'],
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
            'status' => '团体状态：0->冻结，1->正常运行状态',
            'balance' => '团体实时余额，两位小数',
            'name' => '班级名称',
        ];
    }

    /**
     * 关联创建者|User
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(),['user_id'=>'user_id']);
    }

    /**
     * 返回team_ID
     */
    public function getId()
    {
        return $this->team_id;
    }

    /**
     * 返回 创建者邮箱地址
     */
    public function getEmail()
    {
        return $this->creator->email;
    }

    /**
     * 返回 角色：团体
     */
    public function getRole()
    {
        return 'team';
    }

    /**
     * 返回团体当前成员
     */
    public function getMember()
    {
        return $this->hasMany(User::className(),['user_id'=>'user_id'])->viaTable('user_team',['team_id'=>'team_id']);
    }

    /**
     * 返回当前用户是否为团体的拥有者/创建者
     */
    public function isCreator()
    {
        return ($this->user_id == Yii::$app->user->identity->user_id) ? 'success' : '';
    }

    /**
     * 格式化时间戳
     */
    public function createtime()
    {
        return date('y-m-d H:i:s',$this->createtime);
    }

    /**
     * 用户vip 新建一个自己的团体
     */
    public function newone($data)
    {
        if ($this->load($data) && $this->validate()) {
            $this->createtime = time();
            $this->user_id = Yii::$app->user->identity->user_id;
            $this->status = 1;
            if (!$this->save()) {
                throw new \Exception();
            }
            $user_team = new UserTeam();
            $user_team->user_id = $this->user_id;
            $user_team->team_id = $this->team_id;
            if (!$user_team->save()) {
                throw new \Exception();
            }
            return true;
        }
        return false;
    }
}
