<?php

/*witness功能：心愿管理 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '心愿管理';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>

	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
			<li><a href=<?=Url::to(['witness/wish','option'=>'noactivate'])?>>未激活心愿</a></li>
			<li><a href=<?=Url::to(['witness/wish','option'=>'waiting'])?>>未激活心愿</a></li>
			<li><a href=<?=Url::to(['witness/wish','option'=>'approved'])?>>已审核</a></li>
			<li><a href=<?=Url::to(['witness/wish','option'=>'supporting'])?>>资助周期中</a></li>
			<li><a href=<?=Url::to(['witness/wish','option'=>'finished'])?>>已完成</a></li>
		</ul>
		<!-- 横向导航 -->

	<?php if(Yii::$app->user->identity->status == 3): ?>
		<div class="alert alert-warning">
			<p>当前账号及所关联的社区处于待核对状态，心愿管理功能无法正常使用。</p>
			<div>请将相关资料：
				<br><a href='<?=Url::to(['site/error']) ?>' target='blank'>&nbsp;《人恋人平台社区服务协议》&nbsp;</a>
				<br><a href='<?=Url::to(['site/error']) ?>' target='blank'>&nbsp;《承诺书》&nbsp;</a>
				<br>邮寄至：广东省梅州市嘉应学院xxxxxx
			</div>
			<p>如已邮寄，请耐心等待我们开放功能权限，我们会尽快通知您！</p>
		</div>
	<?php else: ?>
		<?php if(Yii::$app->request->get('option')=='noactivate'): ?>
		<h3>未激活心愿码  <?=Html::a('生成心愿码',Url::to(['witness/generate']),['class'=>'btn btn-primary','data-confirm'=>'您确定生成一个心愿码吗？']) ?></h3>
			<?php if (Yii::$app->session->hasFlash('generateWish')): ?>
			<div class="alert alert-success"><?=Yii::$app->session->getFlash('generateWish')?></div>
			<?php endif; ?>
		<table class="table table-hover">
		<tr><th>产生时间</th><th>心愿码</th><th>当前状态</th><th>操作</th></tr>
		<?php foreach ($models as $key => $model): ?>
			<tr class=<?=$model->color()?>>
				<td><?=$model->getTime($model->tokentime) ?></td>
				<td><?=$model->token ?></td>
				<td><?=$model->status() ?></td>
				<td>删除</td>
			</tr>
		<?php endforeach ?>
		</table>
		<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>

		<?php elseif(Yii::$app->request->get('option')=='approved'): ?>

		<?php elseif(Yii::$app->request->get('option')=='supporting'): ?>

		<?php elseif(Yii::$app->request->get('option')=='finished'): ?>

		<?php endif ?>

		<!-- 查询栏 -->
		<!-- <div>
		<nav class="navbar navbar-default">
			<div class="container-fluid"> 
			<div class="navbar-header"><span class="navbar-brand">心愿查询</span></div>
			<form class="navbar-form navbar-left" role="search">
				<div class="form-group"><input type="text" class="form-control" placeholder="输入关键词查询心愿"></div>
				<button type="button" class="btn btn-default" aria-label="Left Align">
					<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
				</button>
			</form>
			</div>
		</nav>
		</div> -->
		<!-- 查询栏 -->
	<?php endif ?>

	</div>
</div>