<p>尊敬的 <?php echo $truename; ?>，您好：</p>

<p>您注册的社区： <span style="color:red;font-weight:700"> <?php echo $community_name; ?> </span>已审核成功，对应的功能权限已经开放！</p>

<?php $url = Yii::$app->urlManager->createAbsoluteUrl(['site/login']); ?>
<p><a href="<?php echo $url; ?>">__点击前往登录__</a></p>

<p>该邮件为系统自动发送，请勿回复！</p>
