<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<!-- 左侧导航栏 -->
<div class="col-lg-2">
	<?=Html::img('./image/witness.jpg',['class' => 'center-block','width' => '120px']); ?>
	<br>
	<ul class="nav nav-pills nav-stacked" id="menu">
		<li role="presentation"><a href=<?=Url::to(['witness/personalinfo'])?>>用户信息</a></li>
		<li role="presentation"><a href=<?=Url::to(['site/message','option'=>'receive'])?>>内外通信</a></li>
		<li role="presentation"><a href=<?=Url::to(['witness/finance'])?>>社区流水</a></li>
  		<li role="presentation"><a href=<?=Url::to(['witness/wish','option'=>'noactivate'])?>>心愿管理</a></li>
  		<li role="presentation"><a href=<?=Url::to(['witness/user','option'=>'manage'])?>>用户管理</a></li>
  		<li role="presentation"><a href=<?=Url::to(['witness/team','option'=>'manage'])?>>团体管理</a></li>
  		<li role="presentation"><a href=<?=Url::to(['site/contact'])?>>问题反馈</a></li>
	</ul>
</div>
