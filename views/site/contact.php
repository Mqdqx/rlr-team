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
    <h3>待完善.......</h3>

</div>

