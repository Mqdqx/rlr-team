<?php

/*sponsor功能：逛对应范围(社区)学生发布的心愿 视图文件*/
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '心愿广场';
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
			<h3>心愿广场</h3>
			<?php if(Yii::$app->session->hasFlash('support')): ?>
				<div class="alert alert-info">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<?=Yii::$app->session->getFlash('support') ?>
				</div>
			<?php endif; ?>
			<table class="table table-hover">
				<tr><th>心愿编号</th><th>审核时间</th><th>总期望金额</th><th>资助周期(单位30天)</th><th>发布者</th><th>隶属社区</th><th>操作</th></tr>
				<?php foreach($models as $key => $model): ?>
				<tr class=<?=$model->color()?>>
					<td><?=Html::tag('button',$model->wish_id, ['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$model->wish_id]) ?></td>
					<td><?=$model->getTime($model->verify_time) ?></td>
					<td>￥<?=$model->money ?></td>
					<td><?=$model->month ?></td>
					<td><?=$model->getUsername('wish') ?></td>
					<td><?=$model->community->community_name ?></td>
					<td>
						<?=Html::a('资助',Url::to([
							'vip/wish',
							'option'=>'support',
							'wish_id'=>$model->wish_id,
						]),['class'=>'btn btn-success btn-xs',
							'data-confirm'=>'您确定资助心愿:'.$model->wish_id.'吗? 您的当前余额必须大于￥'.$model->minbalance]) ?>
					</td>
				</tr>
				<!-- 弹出框 -->
				<div class="modal fade" id="<?=$model->wish_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
				<div class="modal-content">
				    <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title" id="myModalLabel">心愿详情：<?=$model->wish_id ?></h4>
				    </div>
				    <div class="modal-body">
					    <dl class="dl-horizontal">
							<dt>心愿编号：</dt><dd><?=$model->wish_id ?></dd>
							<dt>心愿码：</dt><dd><?=$model->token ?></dd>
							<dt>发布时间：</dt><dd><?=$model->getTime($model->publish_time) ?></dd>
							<dt>当前状态：</dt><dd><?=$model->status() ?></dd>
							<dt>发布者：</dt><dd><?=$model->getTruename() ?></dd>
							<dt>总期望金额：</dt><dd><?=$model->money ?></dd>
							<dt>资助周期：</dt><dd><?=$model->month ?></dd>
							<dt>类别：</dt><dd><?=$model->showLabel() ?></dd>
							<dt>原因描述：</dt><dd><?=Html::encode($model->description) ?></dd>
							<dt>审核员：</dt><dd><?=$model->getUsername('verify') ?></dd>
							<dt>审核时间：</dt><dd><?=$model->getTime($model->verify_time) ?></dd>
							<dt>审核批注：</dt><dd><?=$model->verify_res ?></dd>
						</dl>
				    </div>
				    <div class="modal-footer">
				        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				    </div>
				</div>
				</div>
				</div>
				<!-- 弹出框 -->
				<?php endforeach; ?>
			</table>
			<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>
		</div>
	</div>
</div>