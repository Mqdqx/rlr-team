<p>尊敬的 <?php echo $name; ?>，您好：</p>

<?php if ($role == 'vip'): ?>

<p>您在人恋人平台发布的心愿 <span style="color:red;font-weight:700"> <?php echo $wish_id; ?> </span> 于
<b><?php echo date("Y-m-d H:i:s"); ?></b> 依据约定已转账<span style="color:red;font-weight:700">￥<?php echo $money; ?></span>至您的平台钱包！如需体现，请登录后发起申请！ </p>

<?php elseif ($role == 'sponsor'): ?>

<p>您在人恋人平台资助的心愿 <span style="color:red;font-weight:700"> <?php echo $wish_id; ?> </span> 于
<b><?php echo date("Y-m-d H:i:s"); ?></b> 依据约定从你的平台钱包转出<span style="color:red;font-weight:700">￥<?php echo $money; ?></span></p>
	
	<?php if ($next): ?>
		<p>您当前在平台上的余额已不足肩负下一周期的资助，请及时登录充值</p>
	<?php endif ?>

<?php elseif ($role == 'witness'): ?>

<p>您在人恋人平台见证的心愿 <span style="color:red;font-weight:700"> <?php echo $wish_id; ?> </span> 于
<b><?php echo date("Y-m-d H:i:s"); ?></b> 依据约定本应转账的事件因资助者余额不足而中断，请积极与双方联系交涉，推动心愿的正常进行！</p>

<?php endif; ?>

<p>如有疑问或发现漏洞，请及时联系我们！</p>
<p>该邮件为系统自动发送，请勿回复！</p>
