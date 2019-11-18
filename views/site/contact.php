<?php

/*已经登录账号公共功能：问题反馈 视图文件*/
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\captcha\Captcha;

$this->title = '问题反馈';
$this->params['breadcrumbs'][] = ['label'=>'应用中心','url' => Url::to([Yii::$app->user->identity->role.'/index'])];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <!-- 先渲染左边导航 -->
    <?= $this->renderFile('../views/'.Yii::$app->user->identity->role.'/menu.php') ?>

<div class="col-lg-10">
    <h3><?= Html::encode($this->title) ?></h3>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
            谢谢您的问题反馈！
        </div>

        <p>
            Note that if you turn on the Yii debugger, you should be able
            to view the mail message on the mail panel of the debugger.
                Because the application is in development mode, the email is not sent but saved as
                a file under <code><?= Yii::getAlias(Yii::$app->mailer->fileTransportPath) ?></code>.
                Please configure the <code>useFileTransport</code> property of the <code>mail</code>
                application component to be false to enable email sending.
        </p>

    <?php else: ?>

        <p>
            如果您发现系统中存在着问题，请反馈给我们！
        </p>

            <div class="col-lg-5">

                <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                    <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'email') ?>

                    <?= $form->field($model, 'subject') ?>

                    <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'template' => '{image}{input}',
                    ]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>

    <?php endif; ?>
</div>

</div>

        <!-- 入口 -->
        <h3>待启动的投票活动</h3>
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