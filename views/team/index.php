<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '团体';
$this->params['breadcrumbs'][] = '我的团体';

?>
<ul class="nav nav-pills nav-stacked" id="menu"></ul>
<ul class="nav nav-tabs" id="nav_option"></ul>

<h3>我的团体&nbsp;&nbsp;<?=Html::a('创建团体',Url::to(['team/newone']),['class'=>'btn btn-primary'] ) ?></h3>
<?php if(Yii::$app->session->hasFlash('newTeam')): ?>
<div class="alert alert-success"> 团体：<?=Yii::$app->session->getFlash('newTeam') ?> 创建成功！ </div>
<?php endif; ?>
<table class="table table-hover">
	<tr><th>团体名称</th><th>创建者</th><th>当前人数</th><th>操作</th></tr>
	<?php foreach($models as $key => $model): ?>
		<tr class=<?=$model->isCreator() ?> >
			<td><?=Html::encode($model->name) ?></td>
			<td><?=Html::encode($model->creator->username) ?></td>
			<td><?=$model->getMember()->count() ?></td>
			<td>
				<?=Html::a('详情',Url::to(['team/myteam','team_id'=>$model->team_id]) ,['class'=>'btn btn-primary btn-xs']) ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>
