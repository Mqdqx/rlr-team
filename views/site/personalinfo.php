<?php

/*已经登录账号公共功能：个人信息 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = '个人信息';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>

	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
			<li><a href=<?=Url::to(['site/personalinfo','option'=>'see'])?>>个人信息</a></li>
			<li><a href=<?=Url::to(['site/personalinfo','option'=>'modify'])?>>修改完善</a></li>
			<li><a href=<?=Url::to(['site/personalinfo','option'=>'guardian'])?>>监护人信息</a></li>
		</ul>
		<!-- 横向导航 -->
		<br>
		<?php if(Yii::$app->request->get('option') == 'see'): ?>
		<div>
			<table class="table table-striped table-bordered">
				<tr><th>昵称</th><td><?=Yii::$app->user->identity->username ?></td></tr>
				<tr><th>真实姓名</th><td><?=Yii::$app->user->identity->truename ?></td></tr>
				<tr><th>邮箱地址</th><td><?=Yii::$app->user->identity->email ?></td></tr>
				<tr><th>手机号码</th><td><?=Yii::$app->user->identity->number ?></td></tr>
				<tr><th>性别</th><td><?=Yii::$app->user->identity->sex ?></td></tr>
				<tr><th>生日</th><td><?=Yii::$app->user->identity->birthday ?></td></tr>
				<tr><th>支付宝收款账号</th><td><?=Yii::$app->user->identity->alipay ?></td></tr>
				<tr><th>微信账号</th><td><?=Yii::$app->user->identity->wechat ?></td></tr>
				<tr><th>常居地址</th><td><?=Yii::$app->user->identity->address ?></td></tr>
				<tr><th>工作单位</th><td><?=Yii::$app->user->identity->company ?></td></tr>
				<tr><th>个性签名</th><td><?=Yii::$app->user->identity->remarks ?></td></tr>
				<tr><th>身份证号码</th><td><?=Yii::$app->user->identity->idcard ?></td></tr>
				<tr><th>身份证正面</th><td><img src="<?=Yii::$app->user->identity->idcardfront ?>"></td></tr>
				<tr><th>身份证反面</th><td><img src="<?=Yii::$app->user->identity->idcardback ?>"></td></tr>
			</table>
		</div>
		<?php else: ?>

		<?php endif; ?>

	</div>
</div>