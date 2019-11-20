<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;

$this->title = '团体投票';
$this->params['breadcrumbs'][] = ['label'=>'我的团体','url' => Url::to(['team/index'])];
$this->params['breadcrumbs'][] = '团体：'.Yii::$app->session->get('team')->name;
$showRes = ['text'=>[1=>'胜出',2=>'淘汰'],'class'=>[1=>'btn btn-info btn-xs',2=>'btn btn-warning btn-xs']];
?>

<div class="row">
	<!-- 渲染头部二级横向导航 -->
	<?= $this->renderFile('../views/team/menu.php') ?>
	<!-- 渲染头部二级横向导航 -->
	
	<div class="col-lg-12">
	<?php if(Yii::$app->request->get('option') == 'see'): ?>

		<h3>团体投票活动</h3>
		<?php if(Yii::$app->session->hasFlash('start')): ?>
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			投票活动： <?=Yii::$app->session->getFlash('start') ?> 启动成功！
		</div>
		<?php endif; ?>
		<?=GridView::widget([
			'dataProvider'=>$dataProvider,
			'layout'=>"{items}\n{pager}",
			'emptyText'=>'无资助活动',
			'columns'=>[
				'vote_id',
				['attribute'=>'starttime','value'=>function($model) {return date('y-m-d H:i:s',$model->starttime);}],
				['attribute'=>'endtime','value'=>function($model) {return date('y-m-d H:i:s',$model->endtime);}],
				['label'=>'隶属社区','attribute'=>'community_id','value'=>function($model) {return $model->community->community_name;}],
				['label'=>'当前状态','attribute'=>'status','value'=>function($model) {return $model::$_status[$model->status] ;}],

				[
					'class'=>'yii\grid\ActionColumn',
					'header'=>'投票/详情',
					'template'=>'{detail}',
					'buttons'=>[
						'detail'=>function($url,$model,$key) {
							$button = ['1'=>'进入投票','2'=>'查看结果'];
							return Html::a($button[$model->status],Url::to(['team/vote','option'=>'detail','vote_id'=>$model->vote_id ,'team_id'=>Yii::$app->session['team']->team_id]),['class'=>'btn btn-info btn-xs']);
						}
					],
				],
			],
		]) ?>

		

	<?php elseif(Yii::$app->request->get('option') == 'detail'): ?>

		<h3>
			团体投票活动
			<?=$vote->vote_id ?><?=Html::a('返回上一页',Url::to(['team/vote','option'=>'see','team_id'=>Yii::$app->session->get('team')->team_id ]),['class'=>'btn btn-info']) ?>
			<?php if($vote->status==1 && Yii::$app->session->get('team')->isCreator()): ?>
			<?=Html::a('立即结束投票',Url::to(['team/editvote','team_id'=>Yii::$app->session->get('team')->team_id,'option'=>'endvote','vote_id'=>$vote->vote_id]),['class'=>'btn btn-warning','data-confirm'=>'您确定立即结束投票且统计结果吗？']) ?>
			<?php endif; ?>
		</h3>
		<?php if(Yii::$app->session->hasFlash('voteoneSuccess')): ?>
			<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				投票成功！
			</div>
		<?php elseif(Yii::$app->session->hasFlash('voteoneFail')): ?>
			<div class="alert alert-warning alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				投票失败！服务器繁忙，请稍后重试或反馈此问题！
			</div>
		<?php elseif(Yii::$app->session->hasFlash('noMinballot')): ?>
			<div class="alert alert-warning alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				存在多个心愿最小票数，且有成员未参与投票，已发送邮件告知！请耐心等待可行结果的出现在结算
			</div>
		<?php elseif(Yii::$app->session->hasFlash('reset')): ?>
			<div class="alert alert-warning alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?=Yii::$app->session->getFlash('reset') ?>
			</div>
		<?php elseif(Yii::$app->session->hasFlash('statistics')): ?>
			<div class="alert alert-warning alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?=Yii::$app->session->getFlash('statistics') ?>
			</div>
		<?php endif; ?>
		<table class="table table-striped table-bordered">
			<tr><th class="col-lg-2">投票主题</th><td colspan="3"><?=Html::encode($vote->title) ?></td></tr>
			<tr><th class="col-lg-2">当前状态</th><td colspan="3"><?=Html::encode($vote::$_status[$vote->status]) ?></td></tr>
			<tr><th class="col-lg-2">开始时间</th><td colspan="3"><?=date('y年m月d日',$vote->starttime) ?></td></tr>
			<tr><th class="col-lg-2">自动结束时间</th><td colspan="3"><?=date('y年m月d日',$vote->endtime) ?></td></tr>
			<tr><th class="col-lg-2">候选者</th><th class="col-lg-1">心愿详情</th><th>所得票数  <span style="color:red">(此次投票活动您还剩  <?=$vote->surplus() ?>  票)</span></th><th class="col-lg-1">投票</th></tr>
			<?php foreach($vote->wishs as $wish): ?>
			<?php
				$amount = $wish->getVoteRes($vote->vote_id)->amount;
				$member = Yii::$app->session->get('team')->getMember()->count();
				$per = ($amount/$member)*100;
			?>
			<tr>
				<td class="col-lg-2"><?=$wish->truename ?></td>
				<td class="col-lg-1"><?=Html::tag('button',$wish->wish_id,['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$wish->wish_id]) ?></td>
				<td>
					<div class="progress">
  					<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?=$amount ?>" aria-valuemin="0" aria-valuemax="<?=$member ?>" style="min-width: 2em; width: <?=$per?>%">
  						<?=$amount ?> 票
  					</div>
					</div>
				</td>
				<td class="col-lg-1">
				<?php if ($vote->status == 1): ?>
					<?php if($vote->showButton($wish->wish_id) == 'voted'): ?>
					<?=Html::tag('button','已投一票',['class'=>'btn btn-info btn-xs disabled']) ?>
					<?php elseif($vote->showButton($wish->wish_id) == 'insufficient'): ?>
					<?=Html::tag('button','票数用尽',['class'=>'btn btn-info btn-xs disabled']) ?>
					<?php elseif($vote->showButton($wish->wish_id) == 'vote'): ?>
					<?=Html::a('投其一票',Url::to(['team/vote','option'=>'voteone','vote_id'=>$vote->vote_id,'wish_id'=>$wish->wish_id,'team_id'=>Yii::$app->session->get('team')->team_id]),['class'=>'btn btn-info btn-xs','data-confirm'=>'您确定投其一票吗？']) ?>
					<?php endif; ?>
				<?php elseif($vote->status == 2): ?>
					<?=Html::tag('button',$showRes['text'][$wish->getVoteRes($vote->vote_id)->result],['class'=>$showRes['class'][$wish->getVoteRes($vote->vote_id)->result]]) ?>
				<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php foreach($vote->wishs as $k => $wish): ?>
			<!-- 弹出框 -->
			<div class="modal fade" id="<?=$wish->wish_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
			<div class="modal-content">
			    <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="myModalLabel">心愿编号：<?=$wish->wish_id ?></h4>
			    </div>
			    <div class="modal-body">
			    <?=DetailView::widget([
				    'model' => $wish,
				    'attributes' => [
				        ['label'=>'心愿编号','value'=>$wish->wish_id],
				        ['label'=>'发布时间','value'=>$wish->getTime($wish->publish_time)],
				        ['label'=>'当前状态','value'=>$wish->status()],
				        ['label'=>'发布者','value'=>$wish->truename],
				        ['label'=>'总期望金额','value'=>$wish->money],
				        ['label'=>'资助周期','value'=>$wish->month],
				        ['label'=>'类别','value'=>$wish->showLabel()],
				        ['label'=>'原因描述','value'=>$wish->description],
				        ['label'=>'审核员','value'=>$wish->getUsername('verify')],
				        ['label'=>'审核时间','value'=>$wish->getTime($wish->verify_time)],
				        ['label'=>'审核批注','value'=>$wish->verify_res],
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
	<?php endif; ?>

	</div>
</div>
