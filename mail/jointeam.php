<p>尊敬的<?php echo $email; ?>，您好：</p>

<p>人恋人平台用户 <span style="color:red;font-weight:700"> <?php echo $inviter; ?> </span> 邀请您加入团体 <span style="color:red;font-weight:700"> <?php echo $teamanme; ?> </span> ! </p>

<p>您可以点击以下链接查看详情或处理相关信息！</p>

<?php $url = Yii::$app->urlManager->createAbsoluteUrl(['site/jump','option'=>'jointeam','team_message_id'=>$team_message_id]); ?>
<p><a href="<?php echo $url; ?>">__点击跳转__</a></p>

<p>该邮件为系统自动发送，请勿回复！</p>
