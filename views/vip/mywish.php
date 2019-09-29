<?php

/*vip功能：查看只关于自己的心愿，发布心愿*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '我的心愿';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>

	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
			<li><a href=<?=Url::to(['vip/mywish','option'=>'see'])?>>我的心愿</a></li>			
			<li><a href=<?=Url::to(['vip/mywish','option'=>'newone'])?>>发布心愿</a></li>		
		</ul>
		<!-- 横向导航 -->
		<br>
		<?php if(Yii::$app->request->get('option') == 'see'): ?>

		<?php elseif(Yii::$app->request->get('option') == 'newone'): ?>

			<?php if(Yii::$app->user->identity->noComplete('newWish')): ?>
				<div class="alert alert-warning">您的个人信息：
					<br><b><?=Yii::$app->user->identity->noComplete('newWish') ?></b>
					<br>选项中存在未完善的地方，因此无法使用此功能！
					<br>(隐私项仅用于平台或社区老师联系您，不会对外公开！)		
				</div>
			<?php endif; ?>

		<?php endif; ?>
		<div>

		</div>
	</div>
</div>