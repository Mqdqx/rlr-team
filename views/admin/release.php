<?php

/*admin功能：首页发布 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '首页发布';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>

	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
			<li><a href=<?=Url::to(['admin/release','option'=>'release'])?>>首页发布</a></li>
		</ul>
		<!-- 横向导航 -->
		<div>
			<h3><?= Html::encode($data) ?></h3>

		</div>
	</div>
</div>