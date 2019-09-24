<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<!-- 头部二级导航栏 -->
<div class="col-lg-12">
	<ul class="nav nav-tabs" id="menu">
		<li><a href=<?=Url::to(['team/myteam'])?>>团体信息</a></li>
		<li><a href=<?=Url::to(['team/member'])?>>团体成员</a></li>
		<li><a href=<?=Url::to(['team/finance'])?>>团体财务</a></li>
		<li><a href=<?=Url::to(['team/support'])?>>团体资助</a></li>
		<li><a href=<?=Url::to(['team/vote'])?>>团体投票活动</a></li>
		<li class="disabled"><a href=<?=Url::to(['team/newvote'])?>>发起资助投票</a></li>			
	</ul>
</div>
<!-- 头部二级导航栏 -->
