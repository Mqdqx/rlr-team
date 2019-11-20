<?php

/*admin功能：团体管理 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = '团体管理';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>

	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
		</ul>
		<!-- 横向导航 -->
		<div>
			<?=Html::a('投票定时任务',Url::to(['admin/team','option'=>'cron']),['class'=>'btn btn-info']) ?>
		</div>
		<?php if(isset($result)) {var_dump($result);} ?>
	</div>
</div>