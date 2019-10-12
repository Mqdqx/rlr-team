<?php

/*已登录账号的公共功能：站内通信 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\DetailView;

$this->title = '内外通信';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>

	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
			<li><a href=<?=Url::to(['site/message','option'=>'receive'])?>>收件箱</a></li>
			<li><a href=<?=Url::to(['site/message','option'=>'sent'])?>>已发信息</a></li>
			<li><a href=<?=Url::to(['site/message','option'=>'send'])?>>发送信息</a></li>
			<?php if(Yii::$app->request->get('option') == 'handle'): ?>
			<li><a href=<?=Url::to(['site/message','option'=>'handle','message_id'=>$message->message_id])?>>团体邀请</a></li>
			<?php endif; ?>
		</ul>
	<?php if(Yii::$app->user->identity->noComplete('message')): ?>
		<div class="alert alert-warning">
			您的个人信息：
			<br><b><?=Yii::$app->user->identity->noComplete('message') ?></b>
			<br>选项中存在未完善的地方，因此无法使用此功能！
			<br>(隐私项仅用于平台或社区老师联系您，不会对外公开！)		
		</div>
	<?php else: ?>
		<!-- 横向导航 -->
		<?php if(Yii::$app->request->get('option') == 'receive'): ?>

			<h3>收件箱</h3>
			<table class="table table-hover">
				<tr><th>发送时间</th><th>发送者</th><th>信息类型</th><th>标题</th><th>当前状态</th><th>操作</th></tr>
				<?php foreach($models as $key => $message): ?>
				<tr class=<?=$message->color() ?>>
					<td><?=$message->sendtime() ?></td>
					<td><?=$message->fromUser->username ?></td>
					<td><?=$message->type() ?></td>
					<td><?=$message->title ?></td>
					<td><?=$message->status() ?></td>
					<td>
						<?=Html::tag('button','详情',['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$message->message_id]) ?>
						<?php if($message->status == 1 && $message->type == 0): ?>
						<?=Html::a('标记已读',Url::to(['site/message','option'=>'read','message_id'=>$message->message_id]) ,['class'=>'btn btn-primary btn-xs']) ?>
						<?php elseif($message->status == 2 && $message->type == 0): ?>
						<?=Html::a('标记未读',Url::to(['site/message','option'=>'read','message_id'=>$message->message_id]) ,['class'=>'btn btn-primary btn-xs']) ?>
						<?php elseif($message->status == 0 && $message->type == 2): ?>
						<?=Html::a('处理',Url::to(['site/message','option'=>'handle','message_id'=>$message->message_id]) ,['class'=>'btn btn-primary btn-xs']) ?>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>
			<?php foreach($models as $key => $message): ?>
				<!-- 弹出框 -->
				<div class="modal fade" id="<?=$message->message_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
				<div class="modal-content">
				    <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title" id="myModalLabel">信息编号：<?=$message->message_id ?></h4>
				    </div>
				    <div class="modal-body">
				    <?=DetailView::widget([
					    'model' => $message,
					    'attributes' => [
					        ['label'=>'发送时间','value'=>$message->sendtime()],
					        ['label'=>'收件人','value'=>$message->toUser->username],
					        ['label'=>'发送者','value'=>$message->fromUser->username],
					        ['label'=>'主题','value'=>$message->title],
					        ['label'=>'正文内容','value'=>$message->content],
					        ['label'=>'信息类型','value'=>$message->type()],
					        ['label'=>'当前状态','value'=>$message->status()],
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
		
		<?php elseif(Yii::$app->request->get('option') == 'sent'): ?>

			<h3>已发信息</h3>
			<table class="table table-hover">
				<tr><th>发送时间</th><th>收件人</th><th>信息类型</th><th>标题</th><th>当前状态</th><th>操作</th></tr>
				<?php foreach($models as $key => $message): ?>
				<tr class=<?=$message->color() ?>>
					<td><?=$message->sendtime() ?></td>
					<td><?=$message->toUser->username ?></td>
					<td><?=$message->type() ?></td>
					<td><?=$message->title ?></td>
					<td><?=$message->status() ?></td>
					<td>
						<?=Html::tag('button','详情',['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$message->message_id]) ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>
			<?php foreach($models as $key => $message): ?>
				<!-- 弹出框 -->
				<div class="modal fade" id="<?=$message->message_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
				<div class="modal-content">
				    <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title" id="myModalLabel">信息编号：<?=$message->message_id ?></h4>
				    </div>
				    <div class="modal-body">
				    <?=DetailView::widget([
					    'model' => $message,
					    'attributes' => [
					        ['label'=>'发送时间','value'=>$message->sendtime()],
					        ['label'=>'收件人','value'=>$message->toUser->username],
					        ['label'=>'发送者','value'=>$message->fromUser->username],
					        ['label'=>'主题','value'=>$message->title],
					        ['label'=>'正文内容','value'=>$message->content],
					        ['label'=>'信息类型','value'=>$message->type()],
					        ['label'=>'当前状态','value'=>$message->status()],
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

		<?php elseif(Yii::$app->request->get('option') == 'send'): ?>

			<h3>发送信息</h3>
			<?php if (Yii::$app->session->hasFlash('send')): ?>
			<div class="alert alert-success">发送成功！</div>
			<?php endif; ?>
			<?php
			$form = ActiveForm::begin([
				'id' => 'send-form',
				'layout' => 'horizontal',
				'fieldConfig' => [
		            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
		            'labelOptions' => ['class' => 'col-lg-2 control-label'],
	        	],
	        ]);
			?>
			<?=$form->field($model,'receiver')->textInput(['placeholder'=>'邮箱地址/昵称'])->label('收件人') ?>
			<?=$form->field($model,'title')->textInput(['placeholder'=>'请输入标题'])->label('标题') ?>
			<?=$form->field($model,'content')->textarea(['rows'=>8,'placeholder'=>'请输入内容'])->label('信息内容') ?>

			<div class="form-group">
		        <div class="col-lg-offset-2 col-lg-10">
					<?= Html::submitButton('发送', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定发送信息给该用户吗？如该用户未注册，我们将发送一封邮件邀请他入驻人恋人平台！']) ?>
					<?= Html::resetButton('重置', ['class' => 'btn btn-primary', 'name' => 'reset-button']) ?>
		        </div>
			</div>

			<?php ActiveForm::end(); ?>

		<?php elseif(Yii::$app->request->get('option') == 'handle'): ?>
			<h3>团体邀请</h3>
			<?php if($message->status == 3): ?>
				<div class="alert alert-success">您已经同意了加入该团体！</div>
			<?php elseif($message->status == 4): ?>
				<div class="alert alert-warning">您已拒绝加入该团体！</div>
			<?php endif; ?>
			<p class="lead"> 用户： <mark><?=$message->fromUser->username ?></mark> 邀请您加入 团体： <mark><?=$message->team->name ?></mark> ！ </p>
			<h4>团体信息</h4>
			<?=DetailView::widget([
				'model'=>$message->team,
				'attributes'=>[
					['label'=>'团体名称','value'=>$message->team->name],
					['label'=>'创建时间','value'=>$message->team->createtime()],
					['label'=>'创建者','value'=>$message->team->creator->username],
					['label'=>'团体当前余额','value'=>$message->team->balance],
					['label'=>'团体当前成员人数','value'=>$message->team->getMember()->count()],
				]
			]) ?>
			<?php if($message->status == 0): ?>
			<?=Html::a('同意加入',Url::to(['site/message','option'=>'handle','decision'=>'agree','message_id'=>$message->message_id]),['class'=>'btn btn-info','data-confirm'=>'您确定加入该团体吗？']) ?>
			<?=Html::a('拒绝加入',Url::to(['site/message','option'=>'handle','decision'=>'reject','message_id'=>$message->message_id]),['class'=>'btn btn-danger','data-confirm'=>'您确定拒绝加入该团体吗？']) ?>
			<?php endif; ?>
		
		<?php endif; ?>
	<?php endif; ?>
	</div>
</div>