<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<!-- 头部二级导航栏 -->
<div class="col-lg-12">
	<ul class="nav nav-tabs" id="menu">
		<li><a href=<?=Url::to(['team/myteam','team_id'=>Yii::$app->request->get('team_id')])?>>团体信息</a></li>
		<li><a href=<?=Url::to(['team/member','team_id'=>Yii::$app->request->get('team_id')])?>>团体成员</a></li>
		<li><a href=<?=Url::to(['team/finance','team_id'=>Yii::$app->request->get('team_id')])?>>团体财务</a></li>
		<li><a href=<?=Url::to(['team/support','team_id'=>Yii::$app->request->get('team_id')])?>>团体资助</a></li>
		<li><a href=<?=Url::to(['team/vote','team_id'=>Yii::$app->request->get('team_id')])?>>团体资助活动</a></li>
		<?php if(Yii::$app->session->get('team')->isCreator()): ?>
		<li><a href=<?=Url::to(['team/newvote','team_id'=>Yii::$app->request->get('team_id')])?>>发起资助活动</a></li>			
		<?php endif; ?>
	</ul>
	<ul id="nav_option"></ul>
</div>
<!-- 头部二级导航栏 -->
