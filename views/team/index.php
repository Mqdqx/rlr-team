<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '团体';
$this->params['breadcrumbs'][] = '所有团体';

?>

<h3>所属社区的所有团体</h3>
<div class="col-lg-2">
	<div class="thumbnail">
        <img src="./image/team.png">
        <div class="caption">
            <p>化学1603班</p>
            <p>
            	<a href="<?=Url::to(['team/myteam'])?>" class="btn btn-primary">进入我的团体</a>
            </p>
        </div>
    </div>
</div>
<div class="col-lg-2">
	<div class="thumbnail">
        <img src="./image/team.png">
        <div class="caption">
            <p>化学1705班</p>
            <p>
            	<a href="" class="btn btn-default">详情</a>
            	<a href="" class="btn btn-default">加入</a>
            </p>
        </div>
    </div>
</div>
<div class="col-lg-2">
	<div class="thumbnail">
        <img src="./image/team.png">
        <div class="caption">
            <p>环境1502班</p>
            <p>
            	<a href="" class="btn btn-default">详情</a>
            	<a href="" class="btn btn-default">加入</a>
            </p>
        </div>
    </div>
</div>
