<?php

/* @var $this view:vip/index应用中心 */

$this->title = '应用中心';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<!-- 引用渲染应用中心左侧导航 -->
	<?= $this->render('menu.php') ?>
	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
		</ul>
		<!-- 横向导航 -->
		<h1>默认页：各信息总览.....</h1>
	</div>
</div>