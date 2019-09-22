<?php

/*admin功能：社区管理/新建社区 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
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
		<div>
			<h3><?= Html::encode($data) ?></h3>
			<!-- 查询栏 -->
			<?php if (Yii::$app->request->get('option') == 'manage'): ?>
			<div>
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
			</div>
			<?php endif; ?>
			<!-- 查询栏 -->
			
		</div>
	</div>
</div>