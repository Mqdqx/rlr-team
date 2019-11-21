<?php

/*admin功能：平台财务 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

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
		<h3>平台财务</h3>
		<h4>当前沉淀余额：<?=Html::a($balance, Url::to(['admin/finance']),['class' => 'btn btn-default btn-lg'])?></h4>
		<?php if (Yii::$app->session->hasFlash('transfer')): ?>
			<div class="alert alert-success"><?=Yii::$app->session->getFlash('transfer') ?></div>
		<?php endif ?>
		<?=GridView::widget([
			'dataProvider'=>$dataProvider,
			'layout'=>"{items}\n{pager}",
			'emptyText'=>'当前无数据',
			'columns'=>[
				'flows_id',
				['label'=>'申请者','attribute'=>'out_id','value'=>function($model){return $model->get_name('out');}],
				['label'=>'产生时间','attribute'=>'createtime','value'=>function($model){return date('y-m-d H:i:s',$model->createtime);}],
				'money',
				['attribute'=>'status','value'=>function($model){return $model->type();}],
				['attribute'=>'status','value'=>function($model){return $model->status();}],
				[
					'class'=>'yii\grid\ActionColumn',
					'header'=>'操作',
					'template'=>'{operate}',//展示按钮
					'buttons'=>[
						'operate'=>function($url, $model, $key) {
							return Html::a('转账',Url::to(['admin/finance','option'=>'transfer','flows_id'=>$model->flows_id]),['class'=>'btn btn-info btn-xs','data-confirm'=>'您确定已经银行转账了吗？']);
						}
					],
				],
			],
		]); ?>
	</div>
</div>