<?php

namespace app\models;

use Yii;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * 一些临时量
     */
    public $repassword;//重复密码
    public $agree = true;//同意协议

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     * 定义一些模型数据验证规则
     */
    public function rules()
    {
        return [
            [['number','username','sex'],'required','message'=>'此项不能为空！','on'=>['modify']],
            ['number','match','pattern'=>'/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/','message'=>'手机号码格式错误','on'=>['modify']],
            ['username','match','pattern'=>'/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','message'=>'过长或含有非法字符！','on'=>['modify']],
            ['username', 'unique','message'=>'该昵称已被占用，请重新输入','on'=>['modify']],
            ['number', 'unique','message'=>'该手机号已被占用，请重新输入','on'=>['modify']],
            ['truename','match','pattern'=>'/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u','message'=>'过长或含有非法字符！','on'=>['modify']],
            ['idcard','match','pattern'=>'/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/i','message'=>'身份证号码格式错误','on'=>['modify']],

            ['email','required','message'=>'邮箱地址不能为空！','on'=>['register']],
            ['email', 'email','message' => '邮箱地址格式不正确！', 'on'=>['register']],
            ['email', 'unique','message'=>'该邮箱地址已注册，请登录或激活！！','on'=>['register']],
            ['password','required','message'=>'密码不能为空！','on'=>['register']],
            ['repassword','required','message'=>'重复密码不能为空！','on'=>['register']],
            ['repassword', 'compare', 'compareAttribute'=>'password','message'=>'两次密码输入不一致','on'=>['register']],
            ['agree', 'boolean','on'=>['register']],
            ['agree','compare','compareValue'=>true, 'operator'=>'==','message'=>'必须同意协议方可注册','on'=>['register']],
            //默认验证规则
            [['number', 'email'], 'required'],
            [['number', 'createtime', 'logintime', 'status', 'version'], 'integer'],
            [['balance'], 'number'],
            [['email', 'password', 'token', 'image', 'idcardfront', 'idcardback', 'alipay', 'wechat', 'address', 'company'], 'string', 'max' => 100],
            [['username', 'loginip', 'role', 'truename', 'idcard', 'verification'], 'string', 'max' => 32],
            [['sex', 'birthday'], 'string', 'max' => 30],
            [['remarks'], 'string', 'max' => 250],
        ];
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
    public function attributeLabels()
    {
        return [
            'user_id' => '主键ID',
            'number' => '手机号码',
            'email' => '电子邮箱地址',
            'username' => '用户名，昵称',
            'password' => '密码',
            'createtime' => '创建时间戳',
            'logintime' => '最后一次登录时间',
            'loginip' => '最后一次登录IP地址',
            'status' => '当前状态：0->冻结无法登入状态，1->正常使用状态，2->资料待完善状态，3->完善资料待审核状态，4->未激活阻止登录状态',
            'token' => '注册邮件链接激活参数token',
            'role' => '身份：超级管理员admin,资助者sponsor,在校学生student,见证人/社区管理者witness',
            'truename' => '真实姓名',
            'image' => '头像相对路径',
            'idcard' => '身份证号码',
            'idcardfront' => '身份证正面相对路径',
            'idcardback' => '身份证反面相对路径',
            'verification' => '邮箱/短信验证码',
            'balance' => '余额',
            'alipay' => '支付宝账号',
            'wechat' => '微信账号',
            'sex' => '性别',
            'birthday' => '生日',
            'address' => '常居住地址',
            'company' => '单位/公司',
            'remarks' => '备注',
            'version' => '版本号（乐观锁）',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($user_id)
    {
        return static::findOne(['user_id' => $user_id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * 通过邮箱地址查获本对象
     *
     * @param string $username
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::find()->where(['email' => $email])->one();
    }

    /**
     * 通过手机号码查获本对象
     */
    public static function findByNumber($number)
    {
        return static::find()->where(['number' => $number])->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        //哈希对比，传入密码已数据库中的密码对比
        return password_verify($password,$this->password);
    }

    /**
     * 检测本用户user个人信息完整度，用于是否开放一些权限的依据
     * 返回false 则 完整度 足够
     * 不穿参 则验证所有基本信息是否完整，
     */
    public function noComplete($scene='all')
    {
        $status = false;
        switch ($scene) {
            case 'newWish':
                $status = $this->number==0 ? '手机号码，' : '';
                $status = $this->truename=='' ? $status.'真实姓名，' : $status;
                $status = $this->alipay=='' ? $status.'支付宝收款账号，' : $status;
                break;
            case '':
                
                break;
            case 'all':
                $status = $this->number==0 ? '手机号码，' : '';
                $status = $this->username=='' ? $status.'昵称，' : $status;
                $status = $this->truename=='' ? $status.'真实姓名，' : $status;
                $status = $this->sex=='' ? $status.'性别，' : $status;
                $status = $this->alipay=='' ? $status.'支付宝收款账号，' : $status;
                $status = $this->wechat=='' ? $status.'微信账号，' : $status;
                $status = $this->address=='' ? $status.'常居地址，' : $status;
                $status = $this->company=='' ? $status.'工作单位，' : $status;
                $status = $this->birthday=='' ? $status.'生日，' : $status;
                $status = $this->idcard=='' ? $status.'身份证号码，' : $status;
                $status = $this->idcardfront=='./image/idcardfront.jpg' ? $status.'身份证正面，' : $status;
                $status = $this->idcardback=='./image/idcardback.jpg' ? $status.'身份证反面，' : $status;
                break;
            default:
                $status = '出错';
                break;
        }
        return $status;
    }


    /**
     * 生成一个用于邮件链接激活账户的token
     * $time 生成时间戳
     */
    public function createToken()
    {
        return password_hash($this->email.time(), PASSWORD_DEFAULT);
    }

    /**
     * 获取用户此次登录的IP地址
     * 返回IP地址
     */
    public function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        return $ip_address;
    }

    /**
     * 通过传入角色参数并且在验证表单数据后
     * 发生一封邮件给注册者
     */
    public function register($role,$data)
    {
        //指定验证场景，必须先指定验证场景再load数据
        $this->scenario = "register";
        if ($this->load($data) && $this->validate()) {
            $token = $this->createToken();
            $mailer = Yii::$app->mailer->compose('register',['email'=>$this->email,'token'=>$token]);
            $mailer->setFrom(Yii::$app->params['senderEmail']);
            $mailer->setTo($this->email);
            $mailer->setSubject("人恋人平台-用户激活");
            $this->role = $role;
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            //用于后续save时保证可以通关验证
            $this->repassword = $this->password;
            $this->token = $token;
            $this->status = 4;
            $this->createtime = time();
            if ($mailer->send() && $this->save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 角色修改完善自己的个人资料
     */
    public function modify($data)
    {
        $this->scenario = 'modify';
        if ($this->load($data) && $this->validate()) {

            return (bool)$this->save();
        }
        return false;
    }
}
