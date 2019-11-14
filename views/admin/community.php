<?php

/*admin功能：社区管理/新建社区 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yiier\region\models\Region;

$this->title = '社区管理';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>
	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
			<li><a href=<?=Url::to(['admin/community','option'=>'manage'])?>>社区管理</a></li>
			<?php if (Yii::$app->request->get('option') == 'approve'): ?>
			<li><a href=<?=Url::to(['admin/community','option'=>'approve'])?>>社区审核</a></li>
			<?php endif ?>
		</ul>
		<!-- 横向导航 -->
		<?php if (Yii::$app->request->get('option') == 'manage'): ?>

			<h3>社区管理</h3>
			<?php if (Yii::$app->session->hasFlash('approved')): ?>
			<div class="alert alert-success">
				社区：<?=Yii::$app->session->getFlash('approved')?>，审核成功！对应见证人用户权限开放
			</div>
			<?php endif; ?>
			<?=GridView::widget([
				'dataProvider'=>$dataProvider,
				'layout'=>"{items}\n{pager}",
				'emptyText'=>'当前无数据',
				'columns'=>[
					'community_id',
					'community_name',
					['label'=>'当前最小余额比','attribute'=>'minpercent','value'=>function($model){return $model->minpercent.'%';}],
					['label'=>'见证人','attribute'=>'user_id','value'=>function($model){return $model->user->truename;}],
					['label'=>'所属地','value'=>function($model){return $model->region;}],
					['attribute'=>'status','value'=>function($model){return $model::$status[$model->status];}],
					[
						'class'=>'yii\grid\ActionColumn',
						'header'=>'操作',
						'template'=>'{operate}',//展示按钮
						'buttons'=>[
							'operate'=>function($url, $model, $key) {
								if ($model->status==4) {
									return Html::a('审核',Url::to(['admin/community','option'=>'approve','community_id'=>$model->community_id]),['class'=>'btn btn-info btn-xs']);
								} else {
									return Html::a('详情',Url::to(['admin/community','option'=>'detail','community_id'=>$model->community_id]),['class'=>'btn btn-primary btn-xs']);
								}
							}
						],
					],
				],
			]); ?>

		<?php elseif (Yii::$app->request->get('option') == 'approve'): ?>

			<h3>社区审核</h3>
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
	        	<?=$form->field($community,'community_name')->textInput([])->label('社区名称') ?>
				<?=$form->field($community,'minpercent')->textInput([])->label('最小余额比：%') ?>
				<?=$form->field($community,'province_id')->dropdownList(Region::getProvince())->label('省') ?>
				<?=$form->field($community,'city_id')->dropdownList(Region::getCity($community->province_id))->label('市') ?>
				<?=$form->field($community,'address')->textInput([])->label('具体地址') ?>
				<?=$form->field($community,'truename')->textInput([])->label('见证人姓名') ?>
				<?=$form->field($community,'number')->textInput([])->label('见证人手机号码') ?>
				<?=$form->field($community,'remarks')->textarea(['rows'=>6])->label('备注信息') ?>
	        	<div class="form-group">
		        <div class="col-lg-offset-2 col-lg-10">
					<?= Html::submitButton('审核通过', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
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

		<?php endif ?>
			
		<!-- 查询栏 -->
		<!-- <nav class="navbar navbar-default">
			<div class="container-fluid"> 
			<div class="navbar-header"><span class="navbar-brand">社区查询</span></div>
			<form class="navbar-form navbar-left" role="search">
				<div class="form-group"><input type="text" class="form-control" placeholder="输入关键词查询社区"></div>
				<button type="button" class="btn btn-default" aria-label="Left Align">
					<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
				</button>
			</form>
			</div>
		</nav> -->
		<!-- 查询栏 -->
		
	</div>
</div>