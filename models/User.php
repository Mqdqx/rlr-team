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
     * 定义一些模型数据验证规则
     */
    public function rules()
    {
        return [
            ['email','required','message'=>'邮箱地址不能为空！','on'=>['register']],
            ['email', 'email','message' => '邮箱地址格式不正确！', 'on'=>['register']],
            ['email', 'unique','message'=>'该邮箱地址已注册，请登录或激活！！','on'=>['register']],
            ['password','required','message'=>'密码不能为空！','on'=>['register']],
            ['repassword','required','message'=>'重复密码不能为空！','on'=>['register']],
            ['repassword', 'compare', 'compareAttribute'=>'password','message'=>'两次密码输入不一致','on'=>['register']],
            ['agree', 'boolean','on'=>['register']],
            ['agree','compare','compareValue'=>true, 'operator'=>'==','message'=>'必须同意协议方可注册','on'=>['register']],
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
}
