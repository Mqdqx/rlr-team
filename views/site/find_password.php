<?php

/*受邀请者 找回密码 界面 视图文件*/

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = '找回密码';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<!-- 横向导航 -->
	<ul class="nav nav-tabs" id="menu"></ul>
	<ul class="nav nav-pills" id="nav_option"></ul><br>
	<h3>找回密码</h3>
	<div class="col-lg-12">
	<?php if (Yii::$app->request->get('option')=='reset'): ?>
		<?php
			$form = ActiveForm::begin([
				'layout' => 'horizontal',
				'fieldConfig' => [
		            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
		            'labelOptions' => ['class' => 'col-lg-2 control-label'],
	        	],
			]);
		?>
		<?=$form->field($user,'email')->textInput(['readonly'=>true])->label('邮箱地址')  ?>
		<?=$form->field($user,'password')->passwordInput(['placeholder'=>'请重置密码'])->label('设置密码')  ?>
		<?=$form->field($user,'repassword')->passwordInput(['placeholder'=>'请再次输入密码'])->label('重复密码')  ?>

		<div class="form-group">
	        <div class="col-lg-offset-2 col-lg-10">
				<?= Html::submitButton('密码重设', ['class' => 'btn btn-info', 'data-confirm' => '您确定设置该密码吗？']) ?>
	        </div>
		</div>
		<?php ActiveForm::end(); ?>

	<?php elseif (Yii::$app->request->get('option')=='send'): ?>
		<?php
			$form = ActiveForm::begin([
				'layout' => 'horizontal',
				'fieldConfig' => [
		            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
		            'labelOptions' => ['class' => 'col-lg-2 control-label'],
	        	],
			]);
		?>
		<?=$form->field($user,'email')->textInput(['placeholder'=>'请输入注册时的邮箱地址'])->label('邮箱地址')  ?>

		<div class="form-group">
	        <div class="col-lg-offset-2 col-lg-10">
				<?= Html::submitButton('发送邮件找回密码', ['class' => 'btn btn-info']) ?>
	        </div>
		</div>
		<?php ActiveForm::end(); ?>

	<?php elseif (Yii::$app->request->get('option')=='tip'): ?>
		<div class="alert alert-success">
			<?=Yii::$app->session->getFlash('find_password') ?>
		</div>
	<?php endif; ?>
	</div>
</div>
