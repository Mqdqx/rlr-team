<p>尊敬的<?php echo $email; ?>，您好：</p>

<p>人恋人平台欢迎您，您已成功注册，请点击以下链接激活账号！</p>

<?php $url = Yii::$app->urlManager->createAbsoluteUrl(['site/useractivate','email' => $email,'token'=>$token]); ?>
<p><a href="<?php echo $url; ?>"><?php echo $url; ?></a></p>

<p>首次激活后即可使用以下信息登录，请妥善保管！</p>

<p>您的登录账号为：<span style="color:red;font-weight:700"><?php echo $email; ?></span></p>

<p>该邮件为系统自动发送，请勿回复！</p>
