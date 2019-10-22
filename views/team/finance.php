<?php

/* @var $this yii\web\View 团体财务 视图 */
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
	<div class="col-lg-12">
	<h3>团体财务<?=Html::a('当前余额：￥'.$balance, Yii::$app->request->getAbsoluteUrl(),['class' => 'btn btn-default btn-lg'])?></h3>
		<?php
			$form = ActiveForm::begin([
				'layout'=>'horizontal',
				'fieldConfig'=>['template'=>"
					<div class=\"col-lg-3\">{input}</div>
					<button type\"submit\" class=\"btn btn-info\" data-confirm=\"您确定为该团体充值吗？\">为团体充值</button>\n
					<div class=\"col-lg-3\">{error}</div>"]
			]); 
		?>
		<?=$form->field($model,'money')->textInput(['placeholder'=>'请输入整数充值金额']) ?>
		<?php ActiveForm::end(); ?>
		<?php if(Yii::$app->session->hasFlash('rechargeSuccess')): ?>
			<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				为团体充值成功！
			</div>
		<?php elseif(Yii::$app->session->hasFlash('rechargeFail')): ?>
			<div class="alert alert-warning">为团体充值失败，请稍后再试或反馈此问题！</div>
		<?php endif; ?>
	<table class="table table-hover">
		<tr><th>转账时间</th><th>支出方</th><th>收入方</th><th>金额</th><th>账单类型</th><th>当前状态</th><th>详情</th></tr>
		<?php foreach($models as $key => $flows): ?>
		<tr class=<?=$flows->color() ?>>
			<td><?=$flows->getTime('createtime') ?></td>
			<td><?=$flows->get_name('out') ?></td>
			<td><?=$flows->get_name('in') ?></td>
			<td><?=$flows->money ?></td>
			<td><?=$flows->type() ?></td>
			<td><?=$flows->status() ?></td>
			<td><?=Html::tag('button','详情',['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$flows->flows_id]) ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>
	<?php foreach($models as $key => $flows): ?>
		<!-- 弹出框 -->
		<div class="modal fade" id="<?=$flows->flows_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
		    <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="myModalLabel">流水编号：<?=$flows->flows_id ?></h4>
		    </div>
		    <div class="modal-body">
		    <?=DetailView::widget([
			    'model' => $flows,
			    'attributes' => [
			        ['label'=>'转账时间','value'=>$flows->getTime('createtime')],
			        ['label'=>'完成时间','value'=>$flows->getTime('endtime')],
			        ['label'=>'支出方','value'=>$flows->get_name('out')],
			        ['label'=>'收入方','value'=>$flows->get_name('in')],
			        ['label'=>'金额','value'=>$flows->money],
			        ['label'=>'账单类型','value'=>$flows->type()],
			        ['label'=>'当前状态','value'=>$flows->status()],
			    ],
			])?>
		    </div>
		    <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
		    </div>
		</div>
		</div>
		</div>
	<?php endforeach; ?>
	</div>
</div>