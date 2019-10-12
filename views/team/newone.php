<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = '创建团体';
$this->params['breadcrumbs'][] = ['label'=>'我的团体','url'=>Url::to(['team/index'])];
$this->params['breadcrumbs'][] = '新建团体';

?>
<ul class="nav nav-pills nav-stacked" id="menu"></ul>
<ul class="nav nav-tabs" id="nav_option"></ul>

<h3>新建团体</h3>
<?php
	$form = ActiveForm::begin([
		'id' => 'newTeam-form',
		'layout' => 'horizontal',
		'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-4\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
    	],
    ]);
?>
<?=$form->field($model,'name')->textInput(['placeholder'=>'请输入团体名称'])->label('团体名称') ?>
<div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
		<?= Html::submitButton('创建', ['class' => 'btn btn-info', 'name' => 'new-button', 'data-confirm'=>'您确定进行下一步吗？']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>