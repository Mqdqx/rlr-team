<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = '团体信息';
$this->params['breadcrumbs'][] = ['label'=>'我的团体','url' => Url::to(['team/index'])];
$this->params['breadcrumbs'][] = '团体：'.$model->name;

?>

<div class="row">
	<!-- 渲染头部二级横向导航 -->
	<?= $this->renderFile('../views/team/menu.php') ?>
	<!-- 渲染头部二级横向导航 -->
	<div class="col-lg-6">
		<?=DetailView::widget([
			'model'=>$model,
			'attributes'=>[
				['label'=>'团体名称','value'=>Html::encode($model->name)],
				['label'=>'创建者','value'=>Html::encode($model->creator->username)],
				['label'=>'创建时间','value'=>Html::encode($model->createtime())],
				['label'=>'成员人数','value'=>Html::encode($model->getMember()->count())],
				['label'=>'当前余额','value'=>Html::encode($model->balance)],
			]
		]) ?>
	</div>
</div>
