<?php

/*witness功能：社区流水 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '社区流水';
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

		<!-- 查询栏 -->
		<nav class="navbar navbar-default">
			<div class="container-fluid"> 
			<div class="navbar-header"><span class="navbar-brand">流水</span></div>
			<form class="navbar-form navbar-left" role="search">
				<div class="form-group"><input type="text" class="form-control" placeholder="输入关键词查询流水"></div>
				<button type="button" class="btn btn-default" aria-label="Left Align">
					<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
				</button>
				<span>只能查询所管社区资助周期自动转账的流水</span>
			</form>
			</div>
		</nav>
		<!-- 查询栏 -->

	</div>
</div>