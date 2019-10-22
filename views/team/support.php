<?php

/* @var $this yii\web\View 查看团体资助 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '团体资助';
$this->params['breadcrumbs'][] = ['label'=>'我的团体','url' => Url::to(['team/index'])];
$this->params['breadcrumbs'][] = '团体：'.Yii::$app->session->get('team')->name;

?>

<div class="row">
	<!-- 渲染头部二级横向导航 -->
	<?= $this->renderFile('../views/team/menu.php') ?>
	<!-- 渲染头部二级横向导航 -->
	<div class="col-lg-12">
	<h3>团体资助</h3>
	<table class="table table-hover">
		<tr><th>心愿编号</th><th>心愿者</th><th>总期望金额</th><th>资助周期(单位30天)</th><th>隶属社区</th><th>资助时间</th><th>当前状态</th></tr>
		<?php foreach($models as $key => $model): ?>
		<tr class=<?=$model->color()?>>
			<td><?=Html::tag('button', Html::encode($model->wish_id), ['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$model->wish_id]) ?></td>
			<td><?=$model->truename ?></td>
			<td>￥<?=$model->money ?></td>
			<td><?=$model->month ?></td>
			<td><?=$model->community->community_name ?></td>
			<td><?=$model->getTime($model->locking_time) ?></td>
			<td><?=$model->status() ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>
	<?php foreach($models as $key => $model): ?>
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
					<dt>发布时间：</dt><dd><?=$model->getTime($model->publish_time) ?></dd>
					<dt>当前状态：</dt><dd><?=$model->status() ?></dd>
					<dt>发布者：</dt><dd><?=$model->getTruename() ?></dd>
					<dt>总期望金额：</dt><dd><?=$model->money ?></dd>
					<dt>资助周期：</dt><dd><?=$model->month ?></dd>
					<dt>类别：</dt><dd><?=$model->showLabel() ?></dd>
					<dt>原因描述：</dt><dd><?=Html::encode($model->description) ?></dd>
					<dt>审核员：</dt><dd><?=$model->getUsername('verify') ?></dd>

					<?php if($model->verify_time !== 0): ?>
					<dt>审核时间：</dt><dd><?=$model->getTime($model->verify_time) ?></dd>
					<dt>审核批注：</dt><dd><?=$model->verify_res ?></dd>
					<?php endif; ?>

					<?php if($model->locking_user_id !== 0): ?>
					<dt>资助者：</dt><dd><?=$model->getUsername('sponsor') ?></dd>
					<dt>资助时间：</dt><dd><?=$model->getTime($model->locking_time) ?></dd>
					<?php endif; ?>

					<?php if($model->locking_team_id !== 0): ?>
					<dt>资助团队：</dt><dd><?=$model->getUsername('sponsor') ?></dd>
					<dt>绑定时间：</dt><dd><?=$model->getTime($model->locking_time) ?></dd>
					<?php endif; ?>

					<?php if($model->start_time !== 0): ?>
					<dt>启动时间：</dt><dd><?=$model->getTime($model->start_time) ?></dd>
					<dt>已资助期数：</dt><dd><?=$model->transfered ?></dd>
					<?php endif; ?>

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
	</div>
</div>