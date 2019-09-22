<?php

/*admin功能：平台财务 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '平台财务';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>

	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
		</ul>
		<!-- 横向导航 -->
		<div>
			<h3><?= Html::encode($data) ?></h3>
		</div>
		<hr>
		<h4>当前沉淀余额：<?=Html::a('￥20000.00', Url::to(['admin/finance']),['class' => 'btn btn-default btn-lg'])?></h4>
		<hr>
		<!-- 查询栏 -->
		<nav class="navbar navbar-default">
			<div class="container-fluid"> 
			<div class="navbar-header"><span class="navbar-brand">流水</span></div>
			<form class="navbar-form navbar-left" role="search">
				<div class="form-group"><input type="text" class="form-control" placeholder="输入关键词查询流水"></div>
				<button type="button" class="btn btn-default" aria-label="Left Align">
					<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
				</button>
			</form>
			</div>
		</nav>
		<!-- 查询栏 -->

	</div>
</div>