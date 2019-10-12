<p>尊敬的<?php echo $email; ?>，您好：</p>

<p>人恋人用户：<span style="color:red;font-weight:700"> <?php echo $inviter; ?> </span> 邀请您加入 <span style="color:red;font-weight:700"> 人恋人平台 </span>！ </p>

<p>且在人恋人平台给你留言了一些信息！请点击下列链接完成注册激活！</p>

<?php $url = Yii::$app->urlManager->createAbsoluteUrl(['site/userregister','email' => $email,'token'=>$token]); ?>
<p><a href="<?php echo $url; ?>">__点击注册激活__</a></p>

<p>注册成功后可用以下账号登录人恋人平台！</p>

<p>您的登录账号为：<span style="color:red;font-weight:700"><?php echo $email; ?></span></p>

<p>该邮件为系统自动发送，请勿回复！</p>
