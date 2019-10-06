<?php

/*admin功能：社区管理/新建社区 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;

$this->title = '社区管理';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>
	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
			<li><a href=<?=Url::to(['admin/community','option'=>'manage'])?>>社区管理</a></li>
			<li><a href=<?=Url::to(['admin/community','option'=>'newone'])?>>新建社区</a></li>
		</ul>
		<!-- 横向导航 -->
		
		<?php if(Yii::$app->request->get('option') == 'newone'): ?>
		<h3>新建社区</h3>
			<?php if (Yii::$app->session->hasFlash('newCommunity')): ?>
			<div class="alert alert-success">
				社区：<?=Yii::$app->session->getFlash('newCommunity')?>，新建成功！对应见证人用户权限开放
			</div>
			<?php endif; ?>
		<?php
			$witness = ArrayHelper::map($witness,'user_id','email');
			$form = ActiveForm::begin([
				'id' => 'newone-form',
				'layout' => 'horizontal',
				'fieldConfig' => [
		            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
		            'labelOptions' => ['class' => 'col-lg-2 control-label'],
	        	],
	        ]);
		?>
		<?=$form->field($model,'community_name')->textInput(['placeholder'=>'请输入社区名称'])->label('社区名称') ?>
		<?=$form->field($model,'community_id')->textInput(['placeholder'=>'请定义社区编号'])->label('社区编号') ?>
		<?=$form->field($model,'minpercent')->textInput(['placeholder'=>'请定义社区资助最小余额比'])->label('最小余额比(%)') ?>
		<?=$form->field($model,'user_id')->dropdownList($witness,['prompt'=>'请选择待审核见证人'])->label('关联见证人') ?>
		<?=$form->field($model,'remarks')->textarea(['rows'=>6,'placeholder'=>'选填，请勿超过200字'])->label('备注信息') ?>

		<div class="form-group">
	        <div class="col-lg-offset-1 col-lg-10">
				<?= Html::submitButton('新建', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
				<?= Html::resetButton('重置', ['class' => 'btn btn-primary', 'name' => 'reset-button']) ?>
	        </div>
		</div>

		<?php ActiveForm::end(); ?>
			



		<?php elseif(Yii::$app->request->get('option') == 'manage'): ?>

		<h3>社区管理</h3>
		<div>
		<!-- 查询栏 -->
		<nav class="navbar navbar-default">
			<div class="container-fluid"> 
			<div class="navbar-header"><span class="navbar-brand">社区查询</span></div>
			<form class="navbar-form navbar-left" role="search">
				<div class="form-group"><input type="text" class="form-control" placeholder="输入关键词查询社区"></div>
				<button type="button" class="btn btn-default" aria-label="Left Align">
					<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
				</button>
			</form>
			</div>
		</nav>
		<!-- 查询栏 -->
		</div>
		
		<?php endif; ?>

	</div>
</div>