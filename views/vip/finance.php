<?php

/*vip功能：个人钱包 流水 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\DetailView;

$this->title = '钱包流水';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

$titles = ['flows'=>'账单','recharge'=>'充值','withdraw'=>'提现'];

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>

	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
			<li><a href=<?=Url::to(['vip/finance','option'=>'flows'])?>>账单</a></li>
			<li><a href=<?=Url::to(['vip/finance','option'=>'recharge'])?>>充值</a></li>
			<li><a href=<?=Url::to(['vip/finance','option'=>'withdraw'])?>>提现</a></li>
		</ul>
		<!-- 横向导航 -->
		<div>
			<h3><?=$titles[Yii::$app->request->get('option')] ?></h3>
			<h4>当前余额：<?=Html::a('￥'.Yii::$app->user->identity->balance, Yii::$app->request->getAbsoluteUrl(),['class' => 'btn btn-default btn-lg'])?></h4>
		</div>

		<?php if(Yii::$app->request->get('option') == 'flows'): ?>

		<?php elseif(Yii::$app->request->get('option') == 'recharge'): ?>

			<?php if(Yii::$app->session->hasFlash('paySuccess')): ?>
				<div class="alert alert-success">付款成功！服务器可能存在延迟，请稍后刷新再验证到账！</div>
			<?php elseif(Yii::$app->session->hasFlash('payFail')): ?>
				<div class="alert alert-danger">操作失败！请稍后再试或反馈此问题!</div>
			<?php endif; ?>
			<?php if(Yii::$app->user->identity->balance < 500): ?><!-- 防止恶意测试 -->
			<?php 
				$form = ActiveForm::begin([
					'layout'=>'horizontal',
					'fieldConfig'=>['template'=>"
						<div class=\"col-lg-3\">{input}</div>
						<button type\"submit\" class=\"btn btn-info\" data-confirm=\"您确定充值如上金额吗？我们将跳转到支付界面\">充值</button>\n
						<div class=\"col-lg-3\">{error}</div>"]
				]); 
			?>
			<?=$form->field($model,'money')->textInput(['placeholder'=>'请输入整数充值金额']) ?>
			<?php ActiveForm::end(); ?>
			<?php endif; ?>
		<?php elseif(Yii::$app->request->get('option') == 'withdraw'): ?>
			<?php if(Yii::$app->session->hasFlash('withdraw')): ?>
				<div class="alert alert-success"><?=Yii::$app->session->getFlash('withdraw') ?></div>
			<?php endif; ?>
			<?php 
				$form = ActiveForm::begin([
					'layout'=>'horizontal',
					'fieldConfig'=>['template'=>"
						<div class=\"col-lg-3\">{input}</div>
						<button type\"submit\" class=\"btn btn-info\" data-confirm=\"您确定提现如上金额吗？我们将锁定您输入的金额\">提现</button>\n
						<div class=\"col-lg-3\">{error}</div>"]
				]); 
			?>
			<?=$form->field($model,'money')->textInput(['placeholder'=>'请输入整数提现金额']) ?>
			<?php ActiveForm::end(); ?>
			
		<?php endif; ?>

		<table class="table table-hover">
			<tr><th>转账时间</th><th>支出方</th><th>收入方</th><th>金额</th><th>账单类型</th><th>当前状态</th><th>详情</th></tr>
			<?php foreach($models as $key => $flows): ?>
				<?php if($flows->status==2){continue;} ?>
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
		<!-- 查询栏 
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
		 查询栏 -->

	</div>
</div>
