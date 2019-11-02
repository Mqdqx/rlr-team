<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = '主页-人恋人公益平台';
?>
<h1>主页-人恋人公益平台</h1>
<ul class="nav nav-tabs" id="nav_option">
</ul>
<ul class="nav nav-pills" id="menu">
</ul>
<?php
	echo '今天：'.date('Y-m-d');
	echo "<br>";
	echo '今天0点：'.strtotime(date('Y-m-d'));
	echo "<br>";
	echo '那天：'.date('Y-m-d',1564783200);
	$day = date('Y-m-d',1564783200);
	echo "<br>";
	echo '那天0点：'.strtotime($day);
	echo "<br>";
	echo 1564783200;
	echo '<br>';

	echo '<hr>';
	echo date('YmdHis').mt_rand(100,999);
	echo '<hr>';
?>
<?php

$sid = session_id();

print($sid."\n");
echo '<hr>';

?>
<?=Html::a('邮件测试',Url::to(['site/index','operate'=>'mail']),['class'=>'btn btn-info']) ?>
<br>
<?php if (Yii::$app->session->hasFlash('mail')): ?>
	<div class="alert alert-warning">
		<?php var_dump(Yii::$app->session->getFlash('mail')) ?>
	</div>
<?php endif ?>