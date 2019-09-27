<?php

/* @var $this view:witness/index应用中心 */

use yii\helpers\Url;

$this->title = '应用中心-见证人';
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
		<?php if (Yii::$app->user->identity->status == 3):?>
			<div class="alert alert-warning">
				<p>当前账号及所关联的社区处于待核对状态，许多功能无法正常使用。</p>
				<div>请将相关资料：
					<br><a href='<?=Url::to(['site/error']) ?>' target='blank'>&nbsp;《人恋人平台社区服务协议》&nbsp;</a>
					<br><a href='<?=Url::to(['site/error']) ?>' target='blank'>&nbsp;《承诺书》&nbsp;</a>
					<br>邮寄至：广东省梅州市嘉应学院xxxxxx
				</div>
				<p>如已邮寄，请耐心等待我们开放功能权限，我们会尽快通知您！</p>
			</div>
		<?php endif?>
		<h1>默认页：各信息总览....</h1>
	</div>
</div>