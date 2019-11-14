<?php

/*witness账号公共功能：个人信息 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yiier\region\models\Region;

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
			<?php if (Yii::$app->session->hasFlash('submit')): ?>
				<div class="alert alert-success alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				提交成功，请耐心等待审核！
				</div>
			<?php endif ?>
			<?php if (!$community->community_id): ?>

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
				<?=$form->field($community,'province_id')->dropdownList(Region::getProvince(),['prompt'=>'请选择所属省份'])->label('省') ?>
				<?=$form->field($community,'city_id')->dropdownList([],['prompt'=>'请选择省后选择市'])->label('市') ?>
				<?=$form->field($community,'address')->textInput(['placeholder'=>'请输入镇区街道门牌'])->label('具体地址') ?>
				<?=$form->field($community,'truename')->textInput(['placeholder'=>'隐私项我们将为您保密'])->label('见证人姓名') ?>
				<?=$form->field($community,'number')->textInput(['placeholder'=>'特殊情况联系您的手机号码'])->label('见证人手机号码') ?>
				<?=$form->field($community,'remarks')->textarea(['rows'=>6,'placeholder'=>'选填，请勿超过200字'])->label('备注信息') ?>
	        	<div class="form-group">
		        <div class="col-lg-offset-2 col-lg-10">
					<?= Html::submitButton('提交社区资料', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
		        </div>
				</div>
				<?php ActiveForm::end(); ?>

				<!-- 省市下拉框联动 -->
				<script>
					$(function () {
						$('#community-province_id').on('change',function() {
							$('option:first').prop('disabled',true);
							var params = {
								'data':{'province_id':$(this).val()},
								'dataType':'json',
								'type':'GET',
								'success':function(res) {
									var city = $('#community-city_id');
									city.empty();
									for(let key in res) {
										city.append('<option value='+key+'>'+res[key]+'</option>');
									}
								}
							};
							$.ajax('<?=Url::to(['site/getcity']) ?>',params);
						});
					});
				</script>
				<!-- 省市下拉框联动 -->
			<?php else: ?>
				<?=DetailView::widget([
				    'model' => $community,
				    'attributes' => [
				        ['label'=>'社区编号','value'=>$community->community_id],
				        ['label'=>'社区名称','value'=>$community->community_name],
				        ['label'=>'当前最小余额比','value'=>$community->minpercent.'%'],
				        ['label'=>'所属地','value'=>$community->region],
				        ['label'=>'具体地址','value'=>$community->address],
				        ['label'=>'备注','value'=>$community->remarks],
				        ['label'=>'注册时间','value'=>function($community){return date('Y-m-d H:i:s',$community->createtime);}],
				        ['label'=>'当前状态','value'=>$community::$status[$community->status]],
				        ['label'=>'见证人','value'=>Yii::$app->user->identity->truename],
				        ['label'=>'注册邮箱','value'=>Yii::$app->user->identity->email],
				        ['label'=>'手机号码','value'=>Yii::$app->user->identity->number],
				    ],
				])?>
			<?php endif ?>
			
		<?php elseif (Yii::$app->request->get('option') == 'modify'): ?>

		<?php endif ?>
		</div>
	</div>
</div>

