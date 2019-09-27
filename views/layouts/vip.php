<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* 会员资助者界面模板*/

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => '主页', 'url' => ['/site/index']],
            ['label' => '关于', 'url' => ['/site/about']],
            ['label' => '团体', 'url' => ['/team/index']],
            ['label' => '社区', 'url' => ['/community/index']],
            ['label' => '应用中心', 'url' => ['/vip/index']],
            Yii::$app->user->isGuest ? (
                ['label' => '登录', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    '注销 (' . Yii::$app->user->identity->role . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<!-- 美化左侧边导航栏 -->
<script>
    //功能：传入字符串url和参数name，返回name的值
    function getUrlParam(url,name) {
        var pattern = new RegExp("[?&]"+name+"\=([^&]+)", "g");  
        var matcher = pattern.exec(url);  
        var items = null;  
        if(null != matcher){  
            try{  
                items = decodeURIComponent(decodeURIComponent(matcher[1]));  
            }catch(e){  
                try{  
                    items = decodeURIComponent(matcher[1]);  
                }catch(e){  
                    items = matcher[1];  
                }  
            }
         }  
     return items;  
    }

    //将所在界面对应的导航栏按钮加色
    $(function() {
        //左侧纵向导航栏
        var menu = $('#menu')[0].children;
        for (var n=0;n < menu.length;n++) {
            var theUrl = $(menu[n].children[0]).attr('href');
            if (getUrlParam(theUrl,'r') == getUrlParam(window.location.href,'r')) {
                $(menu[n]).attr('class','active');
            }
        }
        //右侧横向导航栏
        var option = $('#nav_option')[0].children;
        for (var m=0;m < option.length;m++) {
            var theOption = $(option[m].children[0]).attr('href');
            if (getUrlParam(theOption,'option') == getUrlParam(window.location.href,'option')) {
                $(option[m]).attr('class','active');
            }
        }
    });
</script>
<!-- 美化左侧边导航栏 -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
