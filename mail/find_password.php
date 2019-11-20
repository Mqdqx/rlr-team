<p>尊敬的<?php echo $email; ?>，您好：</p>

<p>此邮件为重置密码的链接，点击以下链接重置您的密码</p>

<?php $url = Yii::$app->urlManager->createAbsoluteUrl(['site/find_password','option'=>'reset' ,'email' => $email,'token'=>$token]); ?>
<p><a href="<?php echo $url; ?>">__点击重置密码__</a></p>

<p>如有疑问或发现漏洞，请及时联系我们！</p>
<p>该邮件为系统自动发送，请勿回复！</p>
