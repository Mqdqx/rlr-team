<p>尊敬的 <?php echo $name; ?>，您好：</p>

<?php if ($role == 'vip'): ?>
<p>您在人恋人平台发布的心愿： <span style="color:red;font-weight:700"> <?php echo $wish_id; ?> </span> 已被慷慨人士资助！请积极配合见证人完成后续手续补全！ </p>
<?php elseif ($role == 'witness'): ?>
<p>您在人恋人平台管理的心愿： <span style="color:red;font-weight:700"> <?php echo $wish_id; ?> </span> 已被慷慨人士资助！请积极联系双方尽早补全手续！ </p>
<?php endif ?>

<?php $url = Yii::$app->urlManager->createAbsoluteUrl(['site/login']); ?>
<p><a href="<?php echo $url; ?>">__点击前往登录__</a></p>

<p>该邮件为系统自动发送，请勿回复！</p>
