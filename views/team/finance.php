<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use yii\widgets\DetailView;

$this->title = '团体流水';
$this->params['breadcrumbs'][] = ['label'=>'我的团体','url' => Url::to(['team/index'])];
$this->params['breadcrumbs'][] = '团体：'.Yii::$app->session->get('team')->name;

?>

<div class="row">
	<!-- 渲染头部二级横向导航 -->
	<?= $this->renderFile('../views/team/menu.php') ?>
	<!-- 渲染头部二级横向导航 -->
	<h3>团体财务</h3>
	<h4>当前余额：<?=Html::a('￥'.Yii::$app->session->get('team')->balance, Yii::$app->request->getAbsoluteUrl(),['class' => 'btn btn-default btn-lg'])?></h4>

</div>