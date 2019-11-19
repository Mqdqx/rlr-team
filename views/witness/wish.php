<?php

/*witness功能：心愿管理 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\DetailView;
use dosamigos\datetimepicker\DateTimePicker;

$this->title = '心愿管理';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<!-- 先渲染左边导航 -->
	<?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>

	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
			<li><a href=<?=Url::to(['witness/wish','option'=>'noactivate'])?>>未激活心愿</a></li>
			<li><a href=<?=Url::to(['witness/wish','option'=>'waiting'])?>>待审核心愿</a></li>
			<li><a href=<?=Url::to(['witness/wish','option'=>'approved'])?>>已审核</a></li>
			<li><a href=<?=Url::to(['witness/wish','option'=>'start'])?>>待启动</a></li>
			<li><a href=<?=Url::to(['witness/wish','option'=>'supporting'])?>>资助周期中</a></li>
			<li><a href=<?=Url::to(['witness/wish','option'=>'finished'])?>>已完成</a></li>
		</ul>
		<!-- 横向导航 -->

	<?php if(Yii::$app->user->identity->status == 3): ?>

		<div class="alert alert-warning">
			<p>当前账号及所关联的社区处于待核对状态，心愿管理功能无法正常使用。</p>
			<div>请将相关资料：
				<br><a href='<?=Url::to(['site/error']) ?>' target='blank'>&nbsp;《人恋人平台社区服务协议》&nbsp;</a>
				<br><a href='<?=Url::to(['site/error']) ?>' target='blank'>&nbsp;《承诺书》&nbsp;</a>
				<br>邮寄至：广东省梅州市嘉应学院xxxxxx
			</div>
			<p>如已邮寄，请耐心等待我们开放功能权限，我们会尽快通知您！</p>
		</div>

	<?php else: ?>
		<?php if(Yii::$app->request->get('option')=='noactivate'): ?>

			<h3>未激活心愿码  <?=Html::a('生成心愿码',Url::to(['witness/generate']),['class'=>'btn btn-primary','data-confirm'=>'您确定生成一个心愿码吗？']) ?></h3>
				<?php if (Yii::$app->session->hasFlash('generateWish')): ?>
				<div class="alert alert-success"><?=Yii::$app->session->getFlash('generateWish')?></div>
				<?php endif; ?>
			<table class="table table-hover">
			<tr><th>产生时间</th><th>心愿码</th><th>当前状态</th><th>操作</th></tr>
			<?php foreach ($models as $key => $model): ?>
				<tr class=<?=$model->color()?>>
					<td><?=$model->getTime($model->tokentime) ?></td>
					<td><?=$model->token ?></td>
					<td><?=$model->status() ?></td>
					<td>删除</td>
				</tr>
			<?php endforeach ?>
			</table>
			<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>

		<?php elseif(Yii::$app->request->get('option')=='waiting'): ?>

			<h3>心愿审核</h3>
			<?php if(Yii::$app->session->hasFlash('approved')): ?>
				<div class="alert alert-info">心愿编号：<?=Yii::$app->session->getFlash('approved')?>已审核！</div>
			<?php endif; ?>
			<?php if(!Yii::$app->request->get('approve')): ?>
				<table class="table table-hover">
					<tr><th>心愿编号</th><th>发布时间</th><th>总期望金额</th><th>资助周期(单位30天)</th><th>发布者</th><th>当前状态</th><th>审核操作</th></tr>
					<?php foreach($models as $key => $model): ?>
					<tr class=<?=$model->color()?>>
						<td><?=Html::tag('button',$model->wish_id, ['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$model->wish_id]) ?></td>
						<td><?=$model->getTime($model->publish_time) ?></td>
						<td>￥<?=$model->money ?></td>
						<td><?=$model->month ?></td>
						<td><?=$model->getUsername('wish') ?></td>
						<td><?=$model->status() ?></td>
						<td>
							<?=Html::a('驳回',Url::to([
								'witness/wish',
								'option'=>'waiting',
								'wish_id'=>$model->wish_id,
								'approve'=>'reject'
							]),['class'=>'btn btn-default btn-xs']) ?>
							<?=Html::a('通过',Url::to([
								'witness/wish',
								'option'=>'waiting',
								'wish_id'=>$model->wish_id,
								'approve'=>'accept'
							]),['class'=>'btn btn-success btn-xs']) ?>
						</td>
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
								<dt>心愿码：</dt><dd><?=$model->token ?></dd>
								<dt>发布时间：</dt><dd><?=$model->getTime($model->publish_time) ?></dd>
								<dt>当前状态：</dt><dd><?=$model->status() ?></dd>
								<dt>发布者：</dt><dd><?=$model->getTruename() ?></dd>
								<dt>总期望金额：</dt><dd><?=$model->money ?></dd>
								<dt>资助周期：</dt><dd><?=$model->month ?></dd>
								<dt>类别：</dt><dd><?=$model->showLabel() ?></dd>
								<dt>原因描述：</dt><dd><?=Html::encode($model->description) ?></dd>
								<dt>审核员：</dt><dd><?=$model->getUsername('verify') ?></dd>

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
				<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>
			<?php else: ?>
				<?=Html::a('返回上一页',Yii::$app->request->getReferrer(),['class'=>'btn btn-info btn-sm']) ?><hr>
				<?=DetailView::widget([
				    'model' => $wish,
				    'attributes' => [
				        ['label'=>'心愿编号','value'=>$wish->wish_id],
				        ['label'=>'心愿码','value'=>$wish->token],
				        ['label'=>'发布者','value'=>$wish->getUsername()],
				        ['label'=>'发布时间','value'=>$wish->getTime($wish->publish_time)],
				        ['label'=>'当前状态','value'=>$wish->status()],
				        ['label'=>'总期望金额￥','value'=>$wish->money],
						['label'=>'资助周期(单位:30天)','value'=>$wish->month],
				        ['label'=>'类别','value'=>$wish->showLabel()],
				        ['label'=>'原因描述','value'=>$wish->description],
				        ['label'=>'见证人','value'=>$wish->getUsername('verify')],
				    ],
				])?>
				<?php
					$form = ActiveForm::begin([
						'id' => 'approve-form',
						'layout' => 'horizontal',
						'fieldConfig' => [
	            			'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
	            			'labelOptions' => ['class' => 'col-lg-2 control-label'],
        				],
        			]);
        			$approve = ['reject'=>'驳回','accept'=>'通过'];
				?>
				<?=$form->field($wish,'verify_res')->textarea(['rows'=>6,'placeholder'=>'字数必须小于200！'])->label('审核批注') ?>
				<div class="form-group"><div class="col-lg-offset-2 col-lg-10">
				<?= Html::submitButton($approve[Yii::$app->request->get('approve')], ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
	        	</div></div>
				<?php ActiveForm::end(); ?>
			<?php endif; ?>

		<?php elseif(Yii::$app->request->get('option')=='approved'): ?>
			<h3>已审核心愿</h3>
			<table class="table table-hover">
				<tr><th>心愿编号</th><th>审核时间</th><th>总期望金额</th><th>资助周期(单位30天)</th><th>发布者</th><th>当前状态</th><th>操作</th></tr>
				<?php foreach($models as $key => $model): ?>
				<tr class=<?=$model->color()?>>
					<td><?=Html::tag('button',$model->wish_id, ['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$model->wish_id]) ?></td>
					<td><?=$model->getTime($model->verify_time) ?></td>
					<td>￥<?=$model->money ?></td>
					<td><?=$model->month ?></td>
					<td><?=$model->getUsername('wish') ?></td>
					<td><?=$model->status() ?></td>
					<td><?php if($model->status == 2): ?>
						<?=Html::a('推广',Url::to([
							'witness/wish',
							'option'=>'approved',
							'wish_id'=>$model->wish_id,
							'operate'=>'spread'
						]),['class'=>'btn btn-success btn-xs']) ?>
						<?php elseif($model->status == 9): ?>
						<?=Html::a('删除',Url::to([
							'witness/wish',
							'option'=>'approved',
							'wish_id'=>$model->wish_id,
							'operate'=>'delete'
						]),['class'=>'btn btn-danger btn-xs']) ?>
						<?php endif; ?></td>
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
							<dt>心愿码：</dt><dd><?=$model->token ?></dd>
							<dt>发布时间：</dt><dd><?=$model->getTime($model->publish_time) ?></dd>
							<dt>当前状态：</dt><dd><?=$model->status() ?></dd>
							<dt>发布者：</dt><dd><?=$model->getTruename() ?></dd>
							<dt>总期望金额：</dt><dd><?=$model->money ?></dd>
							<dt>资助周期：</dt><dd><?=$model->month ?></dd>
							<dt>类别：</dt><dd><?=$model->showLabel() ?></dd>
							<dt>原因描述：</dt><dd><?=Html::encode($model->description) ?></dd>
							<dt>审核员：</dt><dd><?=$model->getUsername('verify') ?></dd>
							<dt>审核时间：</dt><dd><?=$model->getTime($model->verify_time) ?></dd>
							<dt>审核批注：</dt><dd><?=$model->verify_res ?></dd>
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
			<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>

		<?php elseif(Yii::$app->request->get('option')=='start'): ?>
			
			<?php if(Yii::$app->session->hasFlash('started')): ?>
				<br><div class="alert alert-info">心愿：<?=Yii::$app->session->getFlash('started') ?></div>
			<?php endif; ?>
			<?php if(!Yii::$app->request->get('operate')): ?>
				<h3>待启动心愿</h3>
				<table class="table table-hover">
					<tr><th>心愿编号</th><th>发布时间</th><th>总期望金额</th><th>资助周期(单位30天)</th><th>发布者</th><th>资助者</th><th>资助时间</th><th>操作</th></tr>
					<?php foreach($models as $key => $model): ?>
					<tr class=<?=$model->color()?>>
						<td><?=Html::tag('button', Html::encode($model->wish_id), ['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$model->wish_id]) ?></td>
						<td><?=$model->getTime($model->publish_time) ?></td>
						<td>￥<?=$model->money ?></td>
						<td><?=$model->month ?></td>
						<td><?=$model->getUsername('wish') ?></td>
						<td><?=$model->getUsername('sponsor') ?></td>
						<td><?=$model->getTime($model->locking_time) ?></td>
						<td>
							<?=Html::a('启动',Url::to([
								'witness/wish',
								'option'=>'start',
								'wish_id'=>$model->wish_id,
								'operate'=>'start'
							]),['class'=>'btn btn-primary btn-xs']) ?>
						</td>
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
								<dt>资助团队：</dt><dd><?=$model->getUsername('sponsor') ?></dd>
								<dt>资助时间：</dt><dd><?=$model->getTime($model->locking_time) ?></dd>
								<?php endif; ?>

								<?php if($model->start_time !== 0): ?>
								<dt>启动时间：</dt><dd><?=$model->getTime($model->start_time) ?></dd>
								<dt>已资助期数：</dt><dd><?=$model->$model->transfered ?></dd>
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
				<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>
			<?php elseif(Yii::$app->request->get('operate') == 'start'): ?>
				<h3>启动心愿</h3>
				<?=Html::a('返回上一页',Yii::$app->request->getReferrer(),['class'=>'btn btn-info btn-sm']) ?><hr>
				<?=DetailView::widget([
				    'model' => $wish,
				    'attributes' => [
				        ['label'=>'心愿编号','value'=>$wish->wish_id],
				        ['label'=>'心愿码','value'=>$wish->token],
				        ['label'=>'发布者','value'=>$wish->getUsername()],
				        ['label'=>'发布时间','value'=>$wish->getTime($wish->publish_time)],
				        ['label'=>'当前状态','value'=>$wish->status()],
				        ['label'=>'类别','value'=>$wish->showLabel()],
				        ['label'=>'原因描述','value'=>$wish->description],
				        ['label'=>'审核时间','value'=>$wish->getTime($wish->verify_time)],
				        ['label'=>'审核批注','value'=>$wish->verify_res],
				        ['label'=>'资助者','value'=>$wish->getUsername('sponsor')],
				        ['label'=>'资助时间','value'=>$wish->getTime($wish->locking_time)],
				    ],
				])?>
				<?php
					$form = ActiveForm::begin([
						'id' => 'start-form',
						'layout' => 'horizontal',
						'fieldConfig' => [
	            			'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
	            			'labelOptions' => ['class' => 'col-lg-2 control-label'],
        				],
        			]);
				?>
				<?=$form->field($wish,'month')->textInput()->label('资助周期') ?>
				<?=$form->field($wish,'per')->textInput()->label('每期金额￥') ?>
				<?=$form->field($wish,'money')->textInput(['readonly' => 'true'])->label('总金额￥') ?>
				<?=$form->field($wish,'protocolFile')->fileInput()->label('协议上传') ?>
				<?=$form->field($wish, '_starttime')->widget(
	        		DateTimePicker::className(), [
	        			// inline too, not bad
				        'inline' => false,
				        'language' => 'zh-CN' , //--设置为中文
				        'clientOptions' => [
				            'autoclose' => true,
				            'startDate'=>date('Y-m-d',time()-86400),
				            //'endDate'=>date('Y-m-d',time()+86400*7),
				            'minView'=>'day',
				            'format' => 'yy-mm-dd hh:ii:ss',
	        			]
	    		]);?>
				<div class="form-group"><div class="col-lg-offset-2 col-lg-10">
				<?= Html::submitButton('启动', ['class' => 'btn btn-info', 'name' => 'start-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
	        	</div></div>
				<?php ActiveForm::end(); ?>
			<?php endif; ?>

		<?php elseif(Yii::$app->request->get('option')=='supporting'): ?>
			<h3>资助周期中</h3>
			<table class="table table-hover">
				<tr><th>心愿编号</th><th>启动时间</th><th>总期望金额</th><th>资助周期(单位30天)</th><th>已捐期数</th><th>发布者</th><th>资助者</th></tr>
				<?php foreach($models as $key => $model): ?>
				<tr class=<?=$model->color()?>>
					<td><?=Html::tag('button', Html::encode($model->wish_id), ['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$model->wish_id]) ?></td>
					<td><?=$model->getTime($model->start_time) ?></td>
					<td>￥<?=$model->money ?></td>
					<td><?=$model->month ?></td>
					<td><?=$model->transfered ?></td>
					<td><?=$model->getUsername('wish') ?></td>
					<td><?=$model->getUsername('sponsor') ?></td>
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
							<dt>资助周期：</dt><dd><?=$model->month ?></dd>
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
							<dt>已资助期数：</dt><dd><?=$model->transfered ?></dd>
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
			<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>

		<?php elseif(Yii::$app->request->get('option')=='finished'): ?>
			<h3>已完成心愿</h3>
			<table class="table table-hover">
				<tr><th>心愿编号</th><th>结束时间</th><th>总期望金额</th><th>资助周期(单位30天)</th><th>发布者</th><th>资助者</th><th>操作</th></tr>
				<?php foreach($models as $key => $model): ?>
				<tr class=<?=$model->color()?>>
					<td><?=Html::tag('button', Html::encode($model->wish_id), ['class'=>'btn btn-info btn-xs','data-toggle'=>'modal','data-target'=>'#'.$model->wish_id]) ?></td>
					<td><?=$model->getTime($model->end_time) ?></td>
					<td>￥<?=$model->money ?></td>
					<td><?=$model->month ?></td>
					<td><?=$model->getUsername('wish') ?></td>
					<td><?=$model->getUsername('sponsor') ?></td>
					<td>删除</td>
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
			<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>

		<?php endif ?>

		<!-- 查询栏 -->
		<!-- <div>
		<nav class="navbar navbar-default">
			<div class="container-fluid"> 
			<div class="navbar-header"><span class="navbar-brand">心愿查询</span></div>
			<form class="navbar-form navbar-left" role="search">
				<div class="form-group"><input type="text" class="form-control" placeholder="输入关键词查询心愿"></div>
				<button type="button" class="btn btn-default" aria-label="Left Align">
					<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
				</button>
			</form>
			</div>
		</nav>
		</div> -->
		<!-- 查询栏 -->
	<?php endif ?>
	<?php  ?>
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
