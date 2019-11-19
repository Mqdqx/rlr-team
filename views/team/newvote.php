<?php

/* @var $this yii\web\View  ！！！ 插件的关键函数 时间格式的字符串 转 时间戳 strtotime($string) ！！！*/ 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\widgets\DetailView;
use dosamigos\datetimepicker\DateTimePicker;

$this->title = '发起团体投票';
$this->params['breadcrumbs'][] = ['label'=>'我的团体','url' => Url::to(['team/index'])];
$this->params['breadcrumbs'][] = '团体：'.Yii::$app->session->get('team')->name;

?>

<div class="row">
	<!-- 渲染头部二级横向导航 -->
	<?= $this->renderFile('../views/team/menu.php') ?>
	<!-- 渲染头部二级横向导航 -->
	<div class="col-lg-12">
	
		<?php if(Yii::$app->session->hasFlash('newone')): ?>
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			投票新建成功！请完善编辑后启动投票！
		</div>
		<?php elseif (Yii::$app->session->hasFlash('blindFail')): ?>
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?=Yii::$app->session->getFlash('blindFail') ?>
		</div>
		<?php endif; ?>

	<?php if ($vote->vote_id): ?>
		<?php
			$vote->vote_wish = $_SESSION['vote_wish'];
			$total = 0;
			foreach ($vote->vote_wish as $wish_id => $wish) {
				$total += $wish->money;
			}
		?>
		<h3>编辑启动资助活动</h3>
		<div class="col-lg-6">
		<table class="table table-striped table-bordered">
			<tr><th class="col-lg-3">资助主题</th><td><?=$vote->title ?></td></tr>
			<tr><th class="col-lg-3">心愿所属社区</th><td><?=$vote->community->community_name ?></td></tr>
			<tr><th class="col-lg-3">候选心愿</th><td>
				<?php foreach ($vote->vote_wish as $wish_id => $wish): ?>
					<?=Html::tag('button',$wish_id,['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$wish_id]); ?>
				<?php endforeach ?>
			</td></tr>
			<tr><th class="col-lg-3">所需最小余额￥</th><td><?=$total*$vote->community->minpercent*0.01 ?></td></tr>
			<tr><th class="col-lg-3">当前团体余额￥</th><td><?=Yii::$app->session['team']->balance ?></td></tr>
			<tr><th class="col-lg-3">总肩负金额￥</th><td><?=$total ?></td></tr>
		</table>
		</div>
		<div class="col-lg-6">
		<?php
				$form = ActiveForm::begin([
					'layout' => 'horizontal',
					'fieldConfig' => [
		            	//'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
		            	'labelOptions' => ['class' => 'col-lg-3 control-label'],
	        		],
				]);
			?>
			<?php if (count($vote->vote_wish) == 1): ?>
			<?= $form->field($vote, 'support_num')->textInput(['value'=>1,'readonly'=>true])->label('最终资助人数'); ?>
			<?php elseif (count($vote->vote_wish) == 0): ?>
			<?= $form->field($vote, 'support_num')->textInput(['placeholder'=>'请从下发心愿池选取候选者','value'=>'','readonly'=>true])->label('最终资助人数'); ?>
			<?php else: ?>
			<?= $form->field($vote, 'support_num')->textInput(['value'=>count($vote->vote_wish)-1,'readonly'=>true])->label('最终资助人数'); ?>
			<?php endif ?>
			

			<?= $form->field($vote, 'candidate_num')->textInput(['value'=>count($vote->vote_wish),'readonly'=>true])->label('当前候选人数'); ?>
			<?= $form->field($vote, '_endtime')->widget(
        		DateTimePicker::className(), [
        			// inline too, not bad
			        'inline' => false,
			        'language' => 'zh-CN' , //--设置为中文
			        'clientOptions' => [
			            'autoclose' => true,
			            'startDate'=>date('Y-m-d',time()+86400*1),
			            'endDate'=>date('Y-m-d',time()+86400*7),
			            'minView'=>'day',
			            'format' => 'yy-mm-dd hh:ii:ss',
        			]
    		]);?>
    		<div class="col-lg-offset-3"><p class="text-info">投票活动可手动提前结束</p></div>
			<div class="form-group">
				<div class="col-lg-offset-3">
					<?= Html::submitButton('立即开始投票活动', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'请核对各项信息后启动投票活动']) ?>
					<?= Html::a('删除此次活动',Url::to(['team/editvote','option'=>'del','team_id'=>Yii::$app->session->get('team')->team_id]),['class'=>'btn btn-primary','data-confirm'=>'您确定放弃此次资助活动吗？']); ?>
				</div>
			</div>
			<?php ActiveForm::end(); ?>
		</div>
		<div class="col-lg-12"><?=GridView::widget([
			'dataProvider'=>$dataProvider,
			'layout'=>"{items}\n{pager}",
			'emptyText'=>'当前心愿池无心愿',
			'columns'=>[
				[
					'class'=>'yii\grid\ActionColumn',
					'header'=>'心愿编号',
					'template'=>'{wish_id}',//展示按钮
					'buttons'=>[
						'wish_id'=>function($url, $model, $key) {return Html::tag('button',$model->wish_id,['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$model->wish_id]);}
					],
				],
				['attribute'=>'verify_time','value'=>function($model) {return date('y-m-d H:i:s',$model->verify_time);}],
				'money',
				'month',
				['label'=>'发布者','attribute'=>'username'],
				['label'=>'隶属社区','attribute'=>'verify_user_id','value'=>function($model) {return $model->community->community_name;}],
				[
					'class'=>'yii\grid\ActionColumn',
					'header'=>'绑定/解绑',
					'template'=>'{bind}',
					'buttons'=>[
						'bind'=>function($url,$model,$key) use($vote){return Html::a($model->isCandidate($vote->vote_wish)['text'],['team/editvote','option'=>'bind','team_id'=>Yii::$app->session->get('team')->team_id,'wish_id'=>$model->wish_id],['class'=>$model->isCandidate($vote->vote_wish)['class-xs']]);}
					],
				],
			],
		]) ?></div>
		
		<!-- 弹出框 -->
		<?php foreach($dataProvider->models as $k => $wish): ?>
			<div class="modal fade" id="<?=$wish->wish_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
			<div class="modal-content">
			    <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="myModalLabel">心愿编号：<?=$wish->wish_id ?></h4>
			    </div>
			    <div class="modal-body">
			    <?=DetailView::widget([
				    'model' => $wish,
				    'attributes' => [
				        ['label'=>'心愿编号','value'=>$wish->wish_id],
				        ['label'=>'发布时间','value'=>$wish->getTime($wish->publish_time)],
				        ['label'=>'当前状态','value'=>$wish->status()],
				        ['label'=>'发布者','value'=>$wish->truename],
				        ['label'=>'总期望金额','value'=>$wish->money],
				        ['label'=>'资助周期','value'=>$wish->month],
				        ['label'=>'类别','value'=>$wish->showLabel()],
				        ['label'=>'原因描述','value'=>$wish->description],
				        ['label'=>'审核员','value'=>$wish->getUsername('verify')],
				        ['label'=>'审核时间','value'=>$wish->getTime($wish->verify_time)],
				        ['label'=>'审核批注','value'=>$wish->verify_res],
				    ],
				])?>
			    </div>
			    <div class="modal-footer">
			    	<?=Html::a($wish->isCandidate($vote->vote_wish)['text'],Url::to(['team/editvote','option'=>'bind','team_id'=>Yii::$app->session->get('team')->team_id,'wish_id'=>$wish->wish_id]),['class'=>$wish->isCandidate($vote->vote_wish)['class']]) ?>
			        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			    </div>
			</div>
			</div>
			</div>
		<?php endforeach; ?>
		<!-- 弹出框 -->
	
	<?php else: ?>

		<h3>新建资助投票活动</h3>
		<?php
			$form = ActiveForm::begin([
				'layout' => 'horizontal',
				'fieldConfig' => [
		            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
		            'labelOptions' => ['class' => 'col-lg-2 control-label'],
	        	],
	        ]);
		?>
		<?=$form->field($vote,'title')->textInput(['placeholder'=>'请输入投票主题'])->label('投票标题') ?>
		<?=$form->field($vote,'community_id')->DropdownList($community,['prompt'=>'请选择社区'])->label('心愿社区') ?>
		<div class="form-group">
	        <div class="col-lg-offset-2 col-lg-10">
				<?= Html::submitButton('新建', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
	        </div>
		</div>
		<?php ActiveForm::end(); ?>

	<?php endif ?>

	</div>
</div>


