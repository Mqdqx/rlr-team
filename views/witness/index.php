<?php

/* @var $this view:witness/index应用中心 */

use yii\helpers\Url;
use yii\helpers\Html;

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
		<?php if (!Yii::$app->user->identity->community): ?>
			<div class="alert alert-warning">
				请前往<a href='<?=Url::to(['witness/personalinfo']) ?>'>&nbsp;用户信息&nbsp;</a>完善相关信息！
			</div>
		<?php endif ?>
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
		<div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
            <h4>
            	<?= Html::a('承诺书模版下载', Yii::$app->request->baseUrl.'/file/承诺书模版.docx',['class' => 'btn btn-info', 'name' => 'view-button']) ?>
            </h4>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
            <h4>
            	<?= Html::a('心愿协议模板下载', Yii::$app->request->baseUrl.'/file/人恋人心愿协议模板.doc',['class' => 'btn btn-info', 'name' => 'view-button']) ?>
            </h4>
            </div>
        </div>
	</div>
</div>