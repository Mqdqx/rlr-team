<?php

/*未登录者 注册界面 视图文件*/

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = '用户注册';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-register">
	<h3><?= Html::encode($this->title) ?></h3>
	<!-- 横向导航 -->
	<ul class="nav nav-tabs" id="nav_option">
		<li><a href=<?=Url::to(['site/register','option'=>'sponsor'])?>>资助者注册</a></li>
		<li><a href=<?=Url::to(['site/register','option'=>'student'])?>>学生注册</a></li>
	</ul>
	<!-- 横向导航 -->
	<!-- 生成渲染注册form -->
	<?php
		/*
		$form = ActiveForm::begin([
			'id' => 'register-form',
			'layout' => 'horizontal',
			'fieldConfig' => [
	            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
	            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        	],
        ]);
    ?>
	<?=$form->field($,'')->textInput(['placeholder'=>''])->label('') ?>
	<?=$form->field($,'')->checkbox([
		'template'=>'<div class="col-lg-offset-1">{input} 已同意：<a href='.Url::to(['site/error']).'target="blank">《人恋人平台服务协议》</a>{error}</div>'
	]) ?>
	
	<div class="form-group">
        <div class="col-lg-offset-1 col-lg-10">
			<?= Html::submitButton('注册', ['class' => 'btn btn-info', 'name' => 'register-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
			<?= Html::resetButton('重置', ['class' => 'btn btn-primary', 'name' => 'reset-button']) ?>
        </div>
	</div>

	<?php ActiveForm::end(); */?>
	<!-- 生成渲染注册form -->

</div>