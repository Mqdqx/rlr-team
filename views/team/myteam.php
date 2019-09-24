<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '我的团体';
$this->params['breadcrumbs'][] = ['label'=>'所有团体','url' => Url::to(['team/index'])];
$this->params['breadcrumbs'][] = '我的团体';

?>

<div class="row">
	<!-- 渲染头部二级横向导航 -->
	<?= $this->renderFile('../views/team/menu.php') ?>
	<!-- 渲染头部二级横向导航 -->
	<div class="col-lg-12">
		<h3><?= Html::encode($data) ?></h3>
	</div>

</div>