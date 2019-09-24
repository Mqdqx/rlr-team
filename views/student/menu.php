<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<!-- 左侧导航栏 -->
<div class="col-lg-2">
	<?=Html::img('./image/student.jpg',['class' => 'center-block','width' => '120px']); ?>
	<br>
	<ul class="nav nav-pills nav-stacked" id="menu">
		<li role="presentation"><a href=<?=Url::to(['site/personalinfo'])?>>个人信息</a></li>
		<li role="presentation"><a href=<?=Url::to(['site/message','option'=>'receive'])?>>站内通信</a></li>
		<li role="presentation"><a href=<?=Url::to(['student/finance'])?>>钱包流水</a></li>
  		<li role="presentation"><a href=<?=Url::to(['student/wish','option'=>'see'])?>>我的心愿</a></li>
  		<li role="presentation"><a href=<?=Url::to(['site/contact'])?>>问题反馈</a></li>
  		<li role="presentation"><a href=<?=Url::to(['student/upgrade'])?>>升级为资助者</a></li>
	</ul>
</div>
