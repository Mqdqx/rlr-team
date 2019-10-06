<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;
use app\models\User;
use app\models\Community;
use app\models\Team;
use app\models\Flows;
use app\models\WishFlows;

/**
 * This is the model class for table "wish".
 *
 * @property int $wish_id 心愿主键ID
 * @property int $user_id 所属用户的user_id
 * @property int $tokentime 产生心愿码时间戳
 * @property string $token 见证人产生心愿码
 * @property string $money 期望金额，两位小数
 * @property int $month 资助周期，单位：30天
 * @property int $transfered 已经转账的期数，单位：月
 * @property int $label 标签：0->无，1->灾祸，2->单亲，3->孤儿and so on....
 * @property int $per 每一期期望金额
 * @property string $filepath 上传补充文件路径
 * @property string $description 描述/原因
 * @property string $verify_res 审核批注
 * @property int $verify_user_id 审核员/见证人/社区管理员(witness)的user_id
 * @property int $verify_time 审核时间戳
 * @property int $publish_time 启动时间戳
 * @property int $start_time 启动时间戳
 * @property int $end_time 整个心愿完成结束时间戳
 * @property int $status 0->心愿码待激活，1->待对应见证人审核，2->审核通过在心愿池待资助者资助，3->资助者绑定待线下协商，4->资助人协商完成进入资助周期了，
 * 5->团体锁定待线下协商中，6->团体协商完成进入资助周期了，7->团体选取投票中，9-审核驳回，10->资助完成
 * @property int $locking_time 锁定时间戳
 * @property int $locking_user_id 锁定的对象id：资助人id
 * @property int $locking_team_id 锁定的对象id：团体id
 * @property int $version 版本号（乐观锁）
 */
class Wish extends \yii\db\ActiveRecord
{
    /**
     * 一些临时变量
     */
    private $_wish = false;
    private static $CrontabRes = [];
    public $protocolFile;

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
            [['token','month','money','per','label'],'required','on'=>['publish']],
            ['description','string','max'=>255,'on'=>['publish']],
            ['money','validateMoney','on'=>['publish','start']],
            ['token','validateToken','on'=>['publish']],//必须放在publish场景的最后

            [['month','money','per'],'required','on'=>['start']],
            [['month','per'],'integer','on'=>['start']],
            [['protocolFile'],'file','skipOnEmpty'=>false,'extensions'=>'docx','maxSize' => 15*1024,'on'=>['start']],

            ['verify_res','required','on'=>['approve']],
            ['verify_res','string','max'=>255,'on'=>['approve']],

            //默认验证规则
            [['user_id', 'tokentime', 'month','transfered' ,'label','verify_user_id', 'verify_time','publish_time' ,'start_time','end_time' ,'status','locking_time' ,'locking_user_id','locking_team_id' ,'version'], 'integer'],
            [['money','per'], 'number'],
            ['money','validateMoney'],//最后一重验证
            [['filepath','token'], 'string', 'max' => 100],
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
            'token' => '心愿码',
            'tokentime' => '产生心愿码时间戳',
            'money' => '期望金额',
            'month' => '资助周期',
            'label' => '标签：0->其它，1->灾祸，2->单亲，3->孤儿and so on....',
            'per' => '每一期期望金额',
            'filepath' => '上传补充文件路径',
            'description' => '描述/原因',
            'verify_res' => '审核批注',
            'verify_user_id' => '审核员/见证人/社区管理员(witness)的user_id',
            'verify_time' => '审核时间戳',
            'publish_time' => '发布心愿时间戳',
            'start_time' => '启动周期时间戳',
            'end_time' => '整个心愿完成结束时间戳',
            'status' => '当前状态',
            'locking_time' => '锁定时间戳',
            'locking_user_id' => '锁定的对象id：资助人id',
            'locking_team_id' => '锁定的对象id：团体id',
            'vision' => '版本号（乐观锁）',
        ];
    }

    /**
     * 后端验证总期望 金额 是否 等于 期数 * 每期期望
     */
    public function validateMoney($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (((float)$this->money) !== ((float)($this->per*$this->month))) {
                $this->addError($attribute, '数据验证错误!');
            }
        }
    }

    /**
     * 验证心愿码
     */
    public function validateToken($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_wish = self::findOne(['token'=>$this->token]);
            if (!$this->_wish || ($this->_wish->user_id !== 0)) {
                $this->addError($attribute, '无效的心愿码!');
            }
        }
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
                $color = '';
                break;
            case '1':
                $color = 'success';
                break;
            case '2':
                $color = 'info';
                break;
            case '3':
                $color = 'warning';
                break;
            case '9':
                $color = 'danger';
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
            case '2':
                $status = '审核通过';
                break;
            case '3':
                $status = '待启动';
                break;
            case '9':
                $status = '审核驳回';
                break;
            default:
                $status = '未知错误';
                break;
        }
        return $status;
    }

    /**
     * 转译类别标签
     */
    public function showLabel()
    {
        $labels = [0=>'其它',1=>'灾祸',2=>'单亲',3=>'孤儿'];
        return $labels[$this->label];
    }

    /**
     * 通过user_id获取各角色的昵称
     * 
     */
    public function getUsername($role = 'wish')
    {
        switch ($role) {
            case 'verify':
                //return User::findOne(['user_id'=>$this->verify_user_id])->username;
                return $this->hasOne(User::className(),['user_id'=>'verify_user_id'])->one()->username;
                break;
            case 'sponsor':
                if ($this->locking_user_id) {
                    $user = User::findOne(['user_id'=>$this->locking_user_id]);
                    $name = $user->username ? $user->username : '匿名用户';
                } elseif($this->locking_team_id) {
                    $name = Team::findOne(['team_id'=>$this->locking_team_id])->name;
                } else {
                    $name = '未知错误';
                }
                return $name;
                break;
            case 'wish':
                //return User::findOne(['user_id'=>$this->user_id])->username;
                return $this->hasOne(User::className(),['user_id'=>'user_id'])->one()->username;
                break;
            default:
                return '传参错误';
                break;
        }
    }

    /**
     * 通过user_id获取各角色的真实姓名
     * 未完善者 返回 昵称 $this->getUsername()
     */
    public function getTruename($role = 'wish')
    {
        switch ($role) {
            case 'wish':
                return User::findOne(['user_id'=>$this->user_id])->truename;
                break;
            
            default:
                return '传参错误';
                break;
        }

    }

    /**
     * 通过verify_user_id获取对应审核社区(见证人)社区名
     * 返回一个社区对象
     */
    public function getCommunity()
    {
        //return Community::findOne(['user_id'=>$this->verify_user_id]);
        return $this->hasOne(Community::className(),['user_id'=>'verify_user_id']);
    }

    /**
     * 返回该心愿的最小余额比
     */
    public function getMinbalance()
    {
        return $this->community->minpercent * 0.01 * $this->money;
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
                return static::find()->where(['status'=>'0','verify_user_id'=>Yii::$app->user->identity->user_id])->orderBy(['tokentime'=>SORT_ASC]);
                break;
            case 'waiting':
                return static::find()->where(['status'=>'1','verify_user_id'=>Yii::$app->user->identity->user_id])->orderBy(['tokentime'=>SORT_ASC]);
                break;
            case 'approved':
                return static::find()
                    ->where(['and','verify_user_id='.Yii::$app->user->identity->user_id,['or', 'status=2', 'status=9']])
                    ->orderBy(['status'=>SORT_ASC,'verify_time'=>SORT_ASC]);
                break;
            case 'start':
                return static::find()
                    ->where(['and','verify_user_id='.Yii::$app->user->identity->user_id,['or', 'status=3', 'status=5']])
                    ->orderBy(['status'=>SORT_ASC,'locking_time'=>SORT_ASC]);
                break;
            case 'supporting':
                return static::find()
                    ->where(['and','verify_user_id='.Yii::$app->user->identity->user_id,['or', 'status=4', 'status=6']])
                    ->orderBy(['status'=>SORT_ASC,'locking_time'=>SORT_ASC]);
                break;
            case 'finished':
                return static::find()
                    ->where(['and','verify_user_id='.Yii::$app->user->identity->user_id,'status=10'])
                    ->orderBy(['status'=>SORT_ASC,'locking_time'=>SORT_ASC]);
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * vip被资助者角色 凭心愿码 发布一个心愿
     */
    public function publish($data)
    {
        $this->scenario = 'publish';  
        if ($this->load($data) && $this->validate()) {
            $this->_wish->user_id = Yii::$app->user->identity->user_id;
            $this->_wish->money = $this->money;
            $this->_wish->month = $this->month;
            $this->_wish->per = $this->per;
            $this->_wish->label = $this->label;
            $this->_wish->description = $this->description;
            $this->_wish->publish_time = time();
            $this->_wish->status = 1;
            return (bool)$this->_wish->save();
        }
        return false;
    }

    /**
     * witness见证人审核心愿
     */
    public function approve($data,$approve)
    {
        $this->scenario = 'approve';
        if ($this->load($data) && $this->validate()) {
            $status = ['accept'=>2 ,'reject'=>9];
            $this->status = $status[$approve];
            $this->verify_time = time();
            return (bool)$this->save();
        }
        return false;
    }

    /**
     * witness见证人启动心愿
     * 设计到文件上传
     */
    public function start($data)
    {
        $this->protocolFile = UploadedFile::getInstance($this, 'protocolFile');
        if (!$this->protocolFile->saveAs('./file/wish/'.$this->wish_id.'.'.$this->protocolFile->extension)) {
            $this->addError('protocolFile','文件上传失败');
            return false;
        }
        $this->scenario = 'default';
        if ($this->load($data) && $this->validate()) {
            $this->filepath ='./file/wish/'.$this->wish_id.'.docx';
            $this->start_time = time();
            $this->status = 4;
            return (bool)$this->save();
        }
        return false;
    }

    /**
     * 计划每天运行一次的定时任务
     * 心愿转账和邮件提醒
     */
    public static function Crontab()
    {
        $models = self::find()->where(['or','status=4','status=6'])->all();
        
        foreach ($models as $key => $model) {
            //$status : 当前时间周期第几区间，若为整型，则位于 端点日
            $status = (strtotime(date('Y-m-d')) - $model->getStartday()) / 2678400;
            $status = ($status >= $model->month) ? $model->month : $status;//最后端点日
            $_status = $status - $model->transfered;//$_status浮点数或整型(端点日/最后端点日) ， 为 当前 欠 多少期，最高为 总期数,

            if ($_status < 1) {
                continue;
            }
            self::handleTransfer($_status,$model);
            
        }

        return self::$CrontabRes;
    }

    /**
     * 获取启动资助周期那天0点时间戳
     */
    public function getStartday()
    {
        $start_day = date('Y-m-d',$this->start_time);
        $start_day_stamp = strtotime($start_day);
        return $start_day_stamp;
    }

    /**
     * 处理资助周期到达转账节点时的操作
     */
    public static function handleTransfer($number,$wish)
    {
        self::$CrontabRes[$wish->wish_id] = ['success'=>0,'insufficient'=>0,'error'=>0];
        for ($i=0; $i < floor($number); $i++) { 
            if ($wish->sponsor->balance < $wish->per) {
                self::$CrontabRes[$wish->wish_id]['insufficient'] ++ ;
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $wish->sponsor->balance = $wish->sponsor->balance - $wish->per;
                    if (!$wish->sponsor->save()) {
                        throw new \Exception();
                    }
                    $wish->user->balance = $wish->user->balance + $wish->per;
                    if (!$wish->user->save()) {
                        throw new \Exception();
                    }
                    $flows = new Flows();
                    if (!$flows->wishTransfer($wish)) {
                        throw new \Exception();
                    }
                    $wish_flows = new WishFlows();
                    $wish_flows->wish_id = $wish->wish_id;
                    $wish_flows->flows_id = $flows->flows_id;
                    if (!$wish_flows->save()) {
                        throw new \Exception();
                    }
                    $wish->transfered  = $wish->transfered + 1;
                    $wish->status = ($wish->transfered == $wish->month) ? 10 : $wish->status;
                    $wish->end_time = time();
                    if (!$wish->save()) {
                        throw new \Exception();
                    }
                    $transaction->commit();
                    self::$CrontabRes[$wish->wish_id]['success'] ++ ;
                } catch (\Exception $e) {
                    $transaction->rollback();
                    self::$CrontabRes[$wish->wish_id]['error'] ++ ;
                }
            }
        }
        //如果为端点日，则发送邮件 给资助者 提醒或催款
        if (is_int($number)) {
            self::emailTransferRes($wish->sponsor,self::$CrontabRes[$wish->wish_id]);
        }
        return 1;
    }

    /**
     * @sponsor,资助者
     * @res,转账任务结果哦
     */
    public static function emailTransferRes($sponsor,$res)
    {
        if ($res['success'] !== 0) {
            echo "succeed send".$res['success']."number \n";
        }
        if ($res['insufficient'] !== 0) {
            echo "balance not enough ".$res['insufficient']." number \n";
        }
        return 1;
    }

    /**
     * 根据locking_..._id返回资助者/资助团体 对象
     */
    public function getSponsor()
    {
        if ($this->locking_user_id) {
            $sponsor = $this->hasOne(User::className(),['user_id'=>'locking_user_id']);
        } else {
            $sponsor = $this->hasOne(User::className(),['user_id'=>'locking_team_id']);
        }
        return $sponsor;
    }

    /**
     * 根据user_id 返回 被资助者 用户对象User
     */
    public function getUser()
    {
        return User::findOne(['user_id'=>$this->user_id]);
    }
}
