<?php

/*已经登录账号公共功能：个人信息 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;

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
			<?php if (Yii::$app->request->get('option') == 'updateGuardian'): ?>
			<li><a href=<?=Url::to(['site/personalinfo','option'=>'updateGuardian'])?>>监护人信息修改</a></li>
			<?php endif ?>
		</ul>
		<!-- 横向导航 -->
		<br>
		<?php if(Yii::$app->request->get('option') == 'see'): ?>
			<?php if(Yii::$app->user->identity->noComplete()): ?>
				<div class="alert alert-warning">您的个人信息：
					<br><?=Yii::$app->user->identity->noComplete() ?>
					<br>选项中存在未完善的地方，可能会影响到您的体验，请尽快完善！
					<br>(隐私项仅用于平台或社区老师联系您，不会对外公开！)		
				</div>
			<?php endif; ?>
			<div>
			<table class="table table-striped table-bordered">
				<tr><th>昵称</th><td><?=Html::encode(Yii::$app->user->identity->username) ?></td></tr>
				<tr><th>真实姓名</th><td><?=Html::encode(Yii::$app->user->identity->truename) ?></td></tr>
				<tr><th>邮箱地址</th><td><?=Html::encode(Yii::$app->user->identity->email) ?></td></tr>
				<tr><th>手机号码</th><td><?=Html::encode(Yii::$app->user->identity->number) ?></td></tr>
				<tr><th>性别</th><td><?=Html::encode(Yii::$app->user->identity->sex) ?></td></tr>
				<tr><th>生日</th><td><?=Html::encode(Yii::$app->user->identity->birthday) ?></td></tr>
				<tr><th>支付宝收款账号</th><td><?=Html::encode(Yii::$app->user->identity->alipay) ?></td></tr>
				<tr><th>微信账号</th><td><?=Html::encode(Yii::$app->user->identity->wechat) ?></td></tr>
				<tr><th>常居地址</th><td><?=Html::encode(Yii::$app->user->identity->address) ?></td></tr>
				<tr><th>工作单位</th><td><?=Html::encode(Yii::$app->user->identity->company) ?></td></tr>
				<tr><th>个性签名</th><td><?=Html::encode(Yii::$app->user->identity->remarks) ?></td></tr>
				<tr><th>身份证号码</th><td><?=Html::encode(Yii::$app->user->identity->idcard) ?></td></tr>
				<tr><th>身份证正面</th><td><img src="<?=Yii::$app->user->identity->idcardfront ?>"></td></tr>
				<tr><th>身份证反面</th><td><img src="<?=Yii::$app->user->identity->idcardback ?>"></td></tr>
			</table>
			</div>

		<?php elseif(Yii::$app->request->get('option') == 'modify'): ?>
			<?php if(Yii::$app->session->hasFlash('modifySuccess')): ?>
				<div class="alert alert-success"><?=Yii::$app->session->getFlash('modifySuccess') ?></div>
			<?php endif; ?>
			<div>
			<?php
				$form = ActiveForm::begin([
					'id' => 'modify-form',
					'layout' => 'horizontal',
					'fieldConfig' => [
			            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
			            'labelOptions' => ['class' => 'col-lg-2 control-label'],
		        	],
	        	]);
		        $sex = ['男'=>'男','女'=>'女','保密'=>'保密'];
        	?>
        	<?=$form->field($model,'username')->textInput(['placeholder'=>'请输入昵称，必填'])->label('昵称') ?>
			<?=$form->field($model,'number')->textInput(['placeholder'=>'请输入手机号码，必填','value'=>''])->label('手机号码') ?>
			<?=$form->field($model,'sex')->dropdownList($sex)->label('性别') ?>
			<?=$form->field($model,'truename')->textInput(['placeholder'=>'真实姓名，选填'])->label('真实姓名') ?>
			<?=$form->field($model,'alipay')->textInput(['placeholder'=>'如提现，银行卡收款账号，选填'])->label('银行卡账号') ?>
			<?=$form->field($model,'wechat')->textInput(['placeholder'=>'微信账号，选填'])->label('微信账号') ?>
			<?=$form->field($model,'idcard')->textInput(['placeholder'=>'身份证号码，选填'])->label('身份证号码') ?>
			<?=$form->field($model,'address')->textInput(['placeholder'=>'常居地址，选填'])->label('常居地址') ?>
			<?=$form->field($model,'company')->textInput(['placeholder'=>'工作单位，选填'])->label('工作单位') ?>
			
			<?=$form->field($model,'remarks')->textarea(['rows'=>6,'placeholder'=>'选填，请勿超过200字'])->label('个性签名') ?>

			<div class="form-group">
	        <div class="col-lg-offset-2 col-lg-10">
				<?= Html::submitButton('保存', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
	        </div>
			</div>
			<?php ActiveForm::end(); ?>
			</div>

		<?php elseif(Yii::$app->request->get('option') == 'guardian'): ?>
			<?php if (Yii::$app->session->hasFlash('guardianUpdate')): ?>
				<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				监护人信息更新！
				</div>
			<?php endif ?>
			<?php if (Yii::$app->user->identity->guardian): ?>
				<?=DetailView::widget([
					'model'=>$model,
					'attributes'=>[
						['label'=>'真实姓名','value'=>Html::encode($model->truename)],
						['label'=>'银行卡号','value'=>Html::encode($model->bankcard)],
						['label'=>'监护关系','value'=>Html::encode($model->relation)],
						['label'=>'手机号码','value'=>Html::encode($model->number)],
						['label'=>'身份证号码','value'=>Html::encode($model->idcard)],
						['label'=>'居住地址','value'=>Html::encode($model->address)],
					]
				]) ?>
				<?=Html::a('修改',Url::to(['site/personalinfo','option'=>'updateGuardian']),['class'=>'btn btn-primary'])  ?>
			<?php else: ?>
				<div>
				<?php
					$form = ActiveForm::begin([
						'id' => 'guardian-form',
						'layout' => 'horizontal',
						'fieldConfig' => [
				            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
				            'labelOptions' => ['class' => 'col-lg-2 control-label'],
			        	],
		        	]);
		        	$relation = ['其它'=>'其它','父子'=>'父子','母子'=>'母子','师生'=>'师生'];
        		?>
        		<?=$form->field($model,'truename')->textInput(['placeholder'=>'监护人真实姓名，必填'])->label('真实姓名') ?>
        		<?=$form->field($model,'bankcard')->textInput(['placeholder'=>'代收款银行卡号'])->label('银行卡号') ?>
        		<?=$form->field($model,'relation')->dropdownList($relation)->label('监护关系') ?>
        		<?=$form->field($model,'number')->textInput(['placeholder'=>'监护人手机号码'])->label('手机号码') ?>
        		<?=$form->field($model,'idcard')->textInput(['placeholder'=>'监护人身份证号码'])->label('身份证号码') ?>
        		<?=$form->field($model,'address')->textInput(['placeholder'=>'监护人住址'])->label('监护人住址') ?>

        		<div class="form-group">
		        <div class="col-lg-offset-2 col-lg-10">
					<?= Html::submitButton('保存', ['class' => 'btn btn-info', 'name' => 'guardian-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
		        </div>
				</div>
				<?php ActiveForm::end(); ?>
				</div>
			<?php endif ?>

		<?php elseif(Yii::$app->request->get('option') == 'updateGuardian'): ?>
			<div>
			<?php
				$form = ActiveForm::begin([
					'id' => 'guardian-form',
					'layout' => 'horizontal',
					'fieldConfig' => [
			            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
			            'labelOptions' => ['class' => 'col-lg-2 control-label'],
		        	],
	        	]);
	        	$relation = ['其它'=>'其它','父子'=>'父子','母子'=>'母子','师生'=>'师生'];
    		?>
    		<?=$form->field($model,'truename')->textInput(['placeholder'=>'监护人真实姓名，必填'])->label('真实姓名') ?>
    		<?=$form->field($model,'bankcard')->textInput(['placeholder'=>'代收款银行卡号'])->label('银行卡号') ?>
    		<?=$form->field($model,'relation')->dropdownList($relation)->label('监护关系') ?>
    		<?=$form->field($model,'number')->textInput(['placeholder'=>'监护人手机号码'])->label('手机号码') ?>
    		<?=$form->field($model,'idcard')->textInput(['placeholder'=>'监护人身份证号码'])->label('身份证号码') ?>
    		<?=$form->field($model,'address')->textInput(['placeholder'=>'监护人住址'])->label('监护人住址') ?>

    		<div class="form-group">
	        <div class="col-lg-offset-2 col-lg-10">
				<?= Html::submitButton('保存', ['class' => 'btn btn-info', 'name' => 'guardian-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
	        </div>
			</div>
			<?php ActiveForm::end(); ?>
			</div>
		<?php endif; ?>

	</div>
</div>