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
	<?php if(Yii::$app->request->get('option') == 'see'): ?>

		<?php if(Yii::$app->session->hasFlash('newone')): ?>
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			投票新建成功！请完善编辑后启动投票！
		</div>
		<?php elseif(Yii::$app->session->hasFlash('start')): ?>
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			投票活动： <?=Yii::$app->session->getFlash('start') ?> 启动成功！
		</div>
		<?php endif; ?>
		<!-- 入口 -->
		<h3>待启动的投票活动 <?=Html::a('新建一个投票',Url::to(['team/newvote','option'=>'new','team_id'=>Yii::$app->session->get('team')->team_id]),['class'=>'btn btn-info']) ?></h3>
		<?=GridView::widget([
			'dataProvider'=>$dataProvider,
			'layout'=>"{items}\n{pager}",
			'emptyText'=>'当前无数据',
			'columns'=>[
				'vote_id',
				'title',
				'support_num',
				'candidate_num',
				['attribute'=>'createtime','value'=>function($model) {return date('y-m-d H:i:s',$model->createtime);}],
				[
					'class'=>'yii\grid\ActionColumn',
					'header'=>'操作',//设置当前列标题
					'template'=>'{edit} {delete}',//展示按钮
					'buttons'=>[
						'edit'=>function($url, $model, $key) {return Html::a('编辑',Url::to(['team/newvote','team_id'=>Yii::$app->session->get('team')->team_id ,'option'=>'edit','vote_id'=>$model->vote_id]),['class'=>'btn btn-info btn-xs']);},
						'delete'=>function($url, $model, $key) {return Html::a('删除',Url::to(['team/newvote','option'=>'delete','team_id'=>Yii::$app->session->get('team')->team_id ,'vote_id'=>$model->vote_id]),['class'=>'btn btn-danger btn-xs','data-confirm'=>'您确定删除该投票吗？']);},
					],
				],
			], 

		]) ?>
	
	<?php elseif(Yii::$app->request->get('option') == 'new'): ?>
		<!-- 新建一个 -->
		<?=Html::a('返回上一页',Yii::$app->request->getReferrer(),['class'=>'btn btn-info btn-sm']) ?>
		<h3>新建投票</h3>
		<?php
			$form = ActiveForm::begin([
				'layout' => 'horizontal',
				'fieldConfig' => [
		            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
		            'labelOptions' => ['class' => 'col-lg-2 control-label'],
	        	],
	        ]);
	        $number = [2=>2,3=>3,4=>4,5=>5,6=>6,7=>7];
		?>
		<?=$form->field($model,'title')->textInput(['placeholder'=>'请输入投票主题'])->label('投票标题') ?>
		<?=$form->field($model,'candidate_num')->DropdownList($number,['prompt'=>'请选择候选者人数'])->label('候选者人数') ?>
		<div class="form-group">
	        <div class="col-lg-offset-2 col-lg-10">
				<?= Html::submitButton('新建', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
	        </div>
		</div>
		<?php ActiveForm::end(); ?>

	<?php elseif(Yii::$app->request->get('option') == 'edit'): ?>
		<!-- 编辑启动一个 -->
		
		<h3>投票活动编辑启动 <?=Html::a('返回上一页',Url::to(['team/newvote','team_id'=>Yii::$app->session->get('team')->team_id,'option'=>'see']),['class'=>'btn btn-info btn-sm']) ?></h3>
		<?php if(Yii::$app->session->hasFlash('overflow')): ?><!-- 溢出 -->
			<div class="alert alert-warning alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				候选人数已满，若想更换，请先移除！
			</div>
		<?php endif; ?>
		<?php if(Yii::$app->session->hasFlash('startFail')): ?><!--  -->
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				投票活动： <?=Yii::$app->session->getFlash('startFail') ?> 启动失败！也许是其它团体抢先资助了这些心愿,请重新选择候选人
			</div>
		<?php endif; ?>
		<?php if(Yii::$app->session->hasFlash('overdue')): ?><!-- 页面下方的心愿表中数据过期 -->
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				该心愿已被其它用户资助，请刷新后重新选择！
			</div>
		<?php endif; ?>

		<div class="col-lg-6">
			<table class="table table-striped table-bordered">
				<tr><th>活动编号</th><td><?=$model->vote_id ?></td></tr>
				<tr><th>投票主题</th><td><?=$model->title ?></td></tr>
				<tr><th>创建时间</th><td><?=date('y-m-d H:i:s',$model->createtime) ?></td></tr>
				<tr><th>候选者人数</th><td><?=$model->candidate_num ?></td></tr>
				<tr><th>候选者心愿</th><td><?=$model->vote_wish() ?></td></tr>
				<tr><th>总期望金额</th><td><?=$model->money ?></td></tr>
			</table>
		</div>

		<?php if(count(Yii::$app->session->get('team')->_vote->vote_wish) == Yii::$app->session->get('team')->_vote->candidate_num): ?>
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
			<?= $form->field($model, '_endtime')->widget(
        		DateTimePicker::className(), [
        			// inline too, not bad
			        'inline' => false,
			        'language' => 'zh-CN' , //--设置为中文
			        'clientOptions' => [
			            'autoclose' => true,
			            'startDate'=>date('Y-m-d',time()+86400),
			            'endDate'=>date('Y-m-d',time()+86400*7),
			            'minView'=>'day',
			            'format' => 'yy-mm-dd hh:ii:ss',
        			]
    		]);?>

			<div class="form-group">
				<div class="col-lg-offset-3">
					<?= Html::submitButton('立即开始投票活动', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'请核对各项信息后启动投票活动']) ?>
				</div>
			</div>
			<?php ActiveForm::end(); ?>
			</div>
		<?php endif; ?>
		<div class="col-lg-12">
		<?=GridView::widget([
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
				/*[
					'class'=>'yii\grid\ActionColumn',
					'header'=>'绑定/解绑',
					'template'=>'{bind}',
					'buttons'=>[
						'bind'=>function($url,$model,$key) {return Html::a('绑定',['team/newvote','option'=>'bind','wish_id'=>$model->wish_id],['class'=>'btn btn-info btn-xs','data-confirm'=>'您确定选取该心愿候选投票吗？']);}
					],
				],*/
			],
		]) ?>
		</div>
		<?php foreach($models as $k => $wish): ?>
			<!-- 弹出框 -->
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
			    	<?=Html::a($wish->isCandidate()['text'],Url::to(['team/newvote','option'=>'bind','team_id'=>Yii::$app->session->get('team')->team_id,'wish_id'=>$wish->wish_id]),['class'=>$wish->isCandidate()['class']]) ?>
			        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			    </div>
			</div>
			</div>
			</div>
		<?php endforeach; ?>
	
	<?php endif; ?>

	<?php //var_dump($model->_endtime); ?>

	</div>
</div>
