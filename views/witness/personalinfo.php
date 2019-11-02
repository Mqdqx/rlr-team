<?php

/*witness账号公共功能：个人信息 视图文件*/
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
			<li><a href=<?=Url::to(['witness/personalinfo'])?>>用户信息</a></li>
			<?php if (Yii::$app->request->get('option') == 'modify'): ?>
				<li><a href=<?=Url::to(['witness/personalinfo','option'=>'modify'])?>>申请修改</a></li>
			<?php endif ?>
		</ul>
		<!-- 横向导航 -->
		<div>
		<?php if (Yii::$app->request->get('option') == ''): ?>
			<h4>社区资料</h4>
			<?php if (!Yii::$app->user->identity->community): ?>

				<?php
					$form = ActiveForm::begin([
						'id' => 'community-form',
						'layout' => 'horizontal',
						'fieldConfig' => [
				            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
				            'labelOptions' => ['class' => 'col-lg-2 control-label'],
			        	],
		        	]);
	        	?>
	        	<?=$form->field($community,'community_name')->textInput(['placeholder'=>'请输入社区名称'])->label('社区名称') ?>
				<?=$form->field($community,'minpercent')->textInput(['placeholder'=>'请定义社区资助最小余额比'])->label('最小余额比：%') ?>
				<?=$form->field($community,'remarks')->textarea(['rows'=>6,'placeholder'=>'选填，请勿超过200字'])->label('备注信息') ?>
	        	<div class="form-group">
		        <div class="col-lg-offset-2 col-lg-10">
					<?= Html::submitButton('提交社区资料', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
		        </div>
				</div>
				<?php ActiveForm::end(); ?>

			<?php else: ?>
				
			<?php endif ?>
			
		<?php elseif (Yii::$app->request->get('option') == 'modify'): ?>

		<?php endif ?>
		</div>
	</div>
</div>