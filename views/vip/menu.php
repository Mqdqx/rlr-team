<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<!-- 左侧导航栏 -->
<div class="col-lg-2">
	<?=Html::img('./image/vip.jpg',['class' => 'center-block','width' => '120px']); ?>
	<br>
	<ul class="nav nav-pills nav-stacked" id="menu">
		<li role="presentation"><a href=<?=Url::to(['site/personalinfo','option'=>'see'])?>>个人信息</a></li>
		<li role="presentation"><a href=<?=Url::to(['site/message','option'=>'receive'])?>>内外通信</a></li>
		<li role="presentation"><a href=<?=Url::to(['vip/finance'])?>>钱包流水</a></li>
  		<li role="presentation"><a href=<?=Url::to(['vip/support'])?>>我的资助</a></li>
  		<li role="presentation"><a href=<?=Url::to(['vip/wish'])?>>心愿广场</a></li>
  		<li role="presentation"><a href=<?=Url::to(['vip/mywish','option'=>'see'])?>>我的心愿</a></li>
  		<li role="presentation"><a href=<?=Url::to(['site/contact'])?>>问题反馈</a></li>
	</ul>
</div>
