<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<!-- 左侧导航栏 -->
<div class="col-lg-2">
	<?=Html::img('./image/admin.jpg',['class' => 'center-block','width' => '120px']); ?>
	<br>
	<ul class="nav nav-pills nav-stacked" id="menu">
		<li role="presentation"><a href=<?=Url::to(['site/message','option'=>'receive'])?>>内外通信</a></li>
		<li role="presentation"><a href=<?=Url::to(['admin/release','option'=>'release'])?>>首页发布</a></li>
		<li role="presentation"><a href=<?=Url::to(['admin/finance'])?>>平台财务</a></li>
  		<li role="presentation"><a href=<?=Url::to(['admin/community','option'=>'manage'])?>>社区管理</a></li>
  		<li role="presentation"><a href=<?=Url::to(['admin/team'])?>>团体管理</a></li>
  		<li role="presentation"><a href=<?=Url::to(['admin/user','option'=>'manage'])?>>用户管理</a></li>
  		<li role="presentation"><a href=<?=Url::to(['admin/wish'])?>>心愿管理</a></li>
	</ul>
</div>
