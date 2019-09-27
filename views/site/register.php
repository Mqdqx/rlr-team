<?php

/*未登录者 注册界面 视图文件*/

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = '注册';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<!-- 横向导航 -->
	<ul class="nav nav-tabs" id="menu">
		<li role="presentation"><a href=<?=Url::to(['site/register_vip'])?>>用户注册</a></li>
		<li role="presentation"><a href=<?=Url::to(['site/register_community'])?>>社区注册</a></li>
	</ul>
	<ul class="nav nav-pills" id="nav_option">
	</ul>
	<br>
	<?php if (Yii::$app->session->hasFlash('successRegister')): ?>
	<div class="alert alert-success">您已经成功注册，请前往邮箱激活用户！如果使用的是QQ邮箱，请留意垃圾箱！！</div>
	<?php endif; ?>
	<!-- 横向导航 -->
	<!-- 生成渲染注册form -->
	<?php
		if (Yii::$app->request->get('r') == 'site/register_community') {
			echo '<h3 class="col-lg-offset-2">社区注册</h3>';
		}
		$form = ActiveForm::begin([
			'id' => 'register-form',
			'layout' => 'horizontal',
			'fieldConfig' => [
	            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
	            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        	],
        ]);
    ?>
	<?=$form->field($model,'email')->textInput(['placeholder'=>'请输入邮箱地址'])->label('邮箱') ?>
	<?=$form->field($model,'password')->passwordInput(['placeholder'=>'请输入密码'])->label('密码') ?>
	<?=$form->field($model,'repassword')->passwordInput(['placeholder'=>'请再次输入密码'])->label('重复密码') ?>
	<?=$form->field($model,'agree')->checkbox([
		'template'=>'<div class="col-lg-offset-2">{input} 已同意：<a href='.Url::to(['site/error']).'target="blank">《人恋人平台服务协议》</a>{error}</div>'
	]) ?>
	
	<div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
			<?= Html::submitButton('注册', ['class' => 'btn btn-info', 'name' => 'register-button']) ?>
			<?= Html::a('去登录',Url::to(['site/login']),['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
	</div>

	<?php ActiveForm::end(); ?>
	<!-- 生成渲染注册form -->
</div>