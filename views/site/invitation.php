<?php

/*受邀请者 注册激活 界面 视图文件*/

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = '注册激活';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<!-- 横向导航 -->
	<ul class="nav nav-tabs" id="menu"></ul>
	<ul class="nav nav-pills" id="nav_option"></ul><br>
	<h3>注册激活</h3>
	<?php
		$form = ActiveForm::begin([
			'layout' => 'horizontal',
			'fieldConfig' => [
	            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
	            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        	],
		]);
	?>
	<?=$form->field($model,'email')->textInput(['readonly'=>true])->label('邮箱地址')  ?>
	<?=$form->field($model,'password')->passwordInput(['placeholder'=>'请设置密码'])->label('设置密码')  ?>
	<?=$form->field($model,'repassword')->passwordInput(['placeholder'=>'请再次输入密码'])->label('重复密码')  ?>
	<?=$form->field($model,'agree')->checkbox([
		'template'=>'<div class="col-lg-offset-2">{input} 已同意：<a href='.Url::to(['site/error']).'target="blank">《人恋人平台服务协议》</a>{error}</div>'
	]) ?>
	<div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
			<?= Html::submitButton('注册激活', ['class' => 'btn btn-info', 'data-confirm' => '您确定设置该密码吗？']) ?>
        </div>
	</div>
	<?php ActiveForm::end(); ?>
</div>
