<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = '提示';
?>
<div class="site-error">
    <ul class="nav nav-tabs" id="nav_option">
    </ul>
    <ul class="nav nav-pills" id="menu">
    </ul>
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('noActivate')): ?>
        <div class="alert alert-danger">
            <p>对不起，该账户尚未激活，无法登录，请前往注册时的邮箱激活或者反馈于<?=Yii::$app->params['adminEmail'];?></p>
        </div>
    <?php elseif (Yii::$app->session->hasFlash('activated')): ?>
        <div class="alert alert-success"><p>激活成功！请前往应用中心完善您的资料以便我们更好的服务您！</p></div>
    <?php else: ?>
        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message)) ?>
        </div>
        <p>这里发生一个请求错误，请反馈给我们！</p>
        <p>如果这是您的企图越权操作，我们已记录！</p>
    <?php endif; ?>
</div>
