<?php

/*sponsor功能：个人钱包 流水 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '钱包流水';
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
		<h4>当前余额：<?=Html::a('￥1000.00', Url::to(['sponsor/finance']),['class' => 'btn btn-default btn-lg'])?>
		<?=Html::a('充值', Url::to(['sponsor/finance']),['class' => 'btn btn-default'])?>
		<?=Html::a('体现', Url::to(['sponsor/finance']),['class' => 'btn btn-default'])?></h4>
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
