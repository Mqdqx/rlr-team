<p>尊敬的 <?php echo $name; ?>，您好：</p>

<?php if ($role == 'vip'): ?>
<p>您在人恋人平台发布的心愿： <span style="color:red;font-weight:700"> <?php echo $wish_id; ?> </span> 即将启动！平台将依据协议中的约定周期性转账，请按时登录查收！ </p>
<?php elseif ($role == 'sponsor'): ?>
<p>您在人恋人平台资助的心愿： <span style="color:red;font-weight:700"> <?php echo $wish_id; ?> </span> 即将启动！平台将依据协议中的约定周期性转账，请在约定日期保持余额足够！ </p>
<?php endif ?>

<p>该邮件为系统自动发送，请勿回复！</p>
