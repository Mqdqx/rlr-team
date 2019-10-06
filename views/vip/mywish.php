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
			<table class="table table-hover">
				<tr><th>心愿编号</th><th>发布时间</th><th>总期望金额</th><th>资助周期(单位30天)</th><th>见证人</th><th>当前状态</th><th>操作</th></tr>
				<?php foreach($models as $key => $model): ?>
				<tr class=<?=$model->color()?>>
					<td><?=Html::tag('button', Html::encode($model->wish_id), ['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$model->wish_id]) ?></td>
					<td><?=$model->getTime($model->publish_time) ?></td>
					<td>￥<?=$model->money ?></td>
					<td><?=$model->month ?></td>
					<td><?=$model->getUsername('verify') ?></td>
					<td><?=$model->status() ?></td>
					<td>冻结</td>
				</tr>
				<!-- 弹出框 -->
				<div class="modal fade" id="<?=$model->wish_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
				<div class="modal-content">
				    <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title" id="myModalLabel">心愿详情：<?=$model->wish_id ?></h4>
				    </div>
				    <div class="modal-body">
					    <dl class="dl-horizontal">
							<dt>心愿编号：</dt><dd><?=$model->wish_id ?></dd>
							<dt>发布时间：</dt><dd><?=$model->getTime($model->publish_time) ?></dd>
							<dt>当前状态：</dt><dd><?=$model->status() ?></dd>
							<dt>发布者：</dt><dd><?=$model->getTruename() ?></dd>
							<dt>总期望金额：</dt><dd><?=$model->money ?></dd>
							<dt>资助周期</dt><dd><?=$model->month ?></dd>
							<dt>类别：</dt><dd><?=$model->showLabel() ?></dd>
							<dt>原因描述：</dt><dd><?=Html::encode($model->description) ?></dd>
							<dt>审核员：</dt><dd><?=$model->getUsername('verify') ?></dd>

							<?php if($model->verify_time !== 0): ?>
							<dt>审核时间：</dt><dd><?=$model->getTime($model->verify_time) ?></dd>
							<dt>审核批注：</dt><dd><?=$model->verify_res ?></dd>
							<?php endif; ?>

							<?php if($model->locking_user_id !== 0): ?>
							<dt>资助者：</dt><dd><?=$model->getUsername('sponsor') ?></dd>
							<dt>资助时间：</dt><dd><?=$model->getTime($model->locking_time) ?></dd>
							<?php endif; ?>

							<?php if($model->locking_team_id !== 0): ?>
							<dt>资助团队：</dt><dd><?=$model->getUsername('team') ?></dd>
							<dt>资助时间：</dt><dd><?=$model->getTime($model->locking_time) ?></dd>
							<?php endif; ?>

							<?php if($model->start_time !== 0): ?>
							<dt>启动时间：</dt><dd><?=$model->getTime($model->start_time) ?></dd>
							<dt>已资助期数：</dt><dd><?=$model->$model->transfered ?></dd>
							<?php endif; ?>

							<?php if($model->end_time !== 0): ?>
							<dt>启动时间：</dt><dd><?=$model->getTime($model->end_time) ?></dd>
							<?php endif; ?>
							
						</dl>
				    </div>
				    <div class="modal-footer">
				        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				    </div>
				</div>
				</div>
				</div>
				<!-- 弹出框 -->
				<?php endforeach; ?>
			</table>
			
		<?php elseif(Yii::$app->request->get('option') == 'newone'): ?>

			<?php if(Yii::$app->user->identity->noComplete('newWish')): ?>
				<div class="alert alert-warning">
					您的个人信息：
					<br><b><?=Yii::$app->user->identity->noComplete('newWish') ?></b>
					<br>选项中存在未完善的地方，因此无法使用此功能！
					<br>(隐私项仅用于平台或社区老师联系您，不会对外公开！)		
				</div>
			<?php else: ?>

				<?php if(Yii::$app->session->hasFlash('published')): ?>
					<div class="alert alert-success">发布成功！请耐心等待审核，该心愿进行时不可再次发布心愿！</div>
				<?php elseif(Yii::$app->user->identity->isWish()): ?>
					<div class="alert alert-info">当前存在未完成的心愿！不可同时发布第二个心愿！</div>
				<?php else: ?>
				<?php
					$form = ActiveForm::begin([
						'id' => 'newwish-form',
						'layout' => 'horizontal',
						'fieldConfig' => [
			            	'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
			            	'labelOptions' => ['class' => 'col-lg-2 control-label'],
		        		],
					]);
					$month = [1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,18=>'1.5年',24=>'2年',36=>'3年'];
					$labels = [0=>'其它',1=>'灾祸',2=>'单亲',3=>'孤儿'];
				?>
				<?=$form->field($model,'token')->textInput(['placeholder'=>'请输入见证人提供的心愿码，必填'])->label('心愿码') ?>
				<?=$form->field($model,'month')->dropdownList($month,['prompt'=>'请选择资助周期，单位：30天'])->label('资助周期') ?>
				<?=$form->field($model,'per')->textInput(['placeholder'=>'请输入每期的期望金额'])->label('每期金额￥') ?>
				<?=$form->field($model,'money')->textInput(['readonly' => 'true'])->label('总金额￥') ?>
				<?=$form->field($model,'label')->dropdownList($labels)->label('分类') ?>
				<?=$form->field($model,'description')->textarea(['rows'=>6,'placeholder'=>'字数必须小于200！'])->label('发布原因') ?>
				<div class="form-group">
	        	<div class="col-lg-offset-2 col-lg-10">
				<?= Html::submitButton('发布', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
	        	</div>
				</div>
				<?php ActiveForm::end(); ?>
				<?php endif; ?>
			<?php endif; ?>

		<?php endif; ?>
		
	</div>
</div>
<!-- 自动计算总期望金额 -->
<script type="text/javascript">
	$(function(){

		$('#wish-month').on('change',function(){
			$('option:first').prop("disabled", true);
			var money = parseFloat($('#wish-month').val()) * parseFloat($('#wish-per').val());
			$('#wish-money').val(money);
			if ($('#wish-money').val() === 'NaN') {$('#wish-money').val('');}
		});
		$('#wish-per').on('change',function(){
			var money = parseFloat($('#wish-month').val()) * parseFloat($('#wish-per').val());
			$('#wish-money').val(money);
			if ($('#wish-money').val() === 'NaN') {$('#wish-money').val('');}
		});
		
	});
</script>
