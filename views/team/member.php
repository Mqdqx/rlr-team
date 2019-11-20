<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use yii\widgets\DetailView;

$this->title = '团体成员';
$this->params['breadcrumbs'][] = ['label'=>'我的团体','url' => Url::to(['team/index'])];
$this->params['breadcrumbs'][] = '团体：'.Yii::$app->session->get('team')->name;

?>

<div class="row">
	<!-- 渲染头部二级横向导航 -->
	<?= $this->renderFile('../views/team/menu.php') ?>
	<!-- 渲染头部二级横向导航 -->
	<div class="col-lg-12">
	<h3>团体成员</h3>
	<?php if(Yii::$app->session->hasFlash('inviteJoinTeam')): ?>
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			成功邀请及邮件予用户：<?=Yii::$app->session->getFlash('inviteJoinTeam') ?> ！
		</div>
	<?php endif; ?>
	<?php 
		$form = ActiveForm::begin([
			'layout'=>'horizontal',
			'fieldConfig'=>['template'=>"
				<div class=\"col-lg-3\">{input}</div>
				<button type\"submit\" class=\"btn btn-info\" data-confirm=\"您确定进行邀请该用户吗？将会发送一则邮件给他\">邀请</button>\n
				<button class=\"btn btn-default\" data-toggle=\"modal\" data-target=\"#invited\">已邀用户</button>\n
				<div class=\"col-lg-3\">{error}</div>"]
		]); 
	?>
	<?=$form->field($invitation,'receiver')->textInput(['placeholder'=>'请输入用户名/邮箱地址邀请用户加入']) ?>
	<?php ActiveForm::end(); ?>
	<table class="table table-hover">
		<tr><th>成员昵称</th><th>手机号码</th><th>邮箱地址</th><th>性别</th><th>贡献值</th><th>更多</th></tr>
		<?php foreach($models as $key => $member): ?>
			<tr class=<?=$member->isCreator() ?>>
				<td><?=$member->username ?></td>
				<td><?=$member->number ?></td>
				<td><?=$member->email ?></td>
				<td><?=$member->sex ?></td>
				<td>功能待完善</td>
				<td>留言功能</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>
	<!-- 弹出框：已经邀请了的用户了 -->
	<div class="modal fade" id="invited" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
	<div class="modal-content">
	    <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">已被邀请的用户</h4>
	    </div>
	    <div class="modal-body">
	    <table class="table table-condensed">
	    	<tr><th>邀请时间</th><th>邀请者</th><th>受邀者</th><th>当前状态</th></tr>
		    <?php foreach($invitedUsers as $k => $invitedUser): ?>
		    	<tr>
		    		<td><?=date('y年m月d日',$invitedUser->message->sendtime) ?></td>
		    		<td><?=$invitedUser->message->fromUser->username ?></td>
		    		<td><?=$invitedUser->message->toUser->username ?></td>
		    		<td><?=$invitedUser->message->status() ?></td>
		    	</tr>
			<?php endforeach; ?>
		</table>
	    </div>
	    <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
	    </div>
	</div>
	</div>
	</div>
	</div>
</div>
<?php //var_dump($invitation) ?>
