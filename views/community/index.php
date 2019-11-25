<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '社区';
$id = Yii::$app->request->get('id');
if ($id) {$this->title .= '-'.$community->community_name;}

?>

<ul class="nav nav-tabs" id="nav_option">
</ul>
<ul class="nav nav-pills" id="menu">
</ul>

<?php if ($id): ?>
	<?php
		$tokens = (new \yii\db\Query())->select(['token'])->where(['status'=>0,'verify_user_id'=>$community->user->user_id])->from('wish')->all();
	?>
	<div class="alert alert-info">
		<p>当前社区可用心愿码：(由社区管理员生成)</p>
		<?php foreach ($tokens as $k => $token): ?>
			<p><?=$token['token'] ?></p>
		<?php endforeach ?>
	</div>
	<h1><?=$community->community_name ?></h1>
	<div id="myCarousel" class="carousel slide" data-ride="carousel">
		<ol class="carousel-indicators">
		<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
		<li data-target="#myCarousel" data-slide-to="1"></li>
		<li data-target="#myCarousel" data-slide-to="2"></li>
		</ol>
		<div class="carousel-inner" role="listbox">
		<div class="item active">
		<img class="first-slide" src="./image/head-1.jpg" alt="First slide">
		<div class="container">
		<div class="carousel-caption">
		</div>
		</div>
		</div>
		<div class="item">
		<img class="second-slide" src="./image/head-2.jpg" alt="Second slide">
		<div class="container">
		<div class="carousel-caption">
		</div>
		</div>
		</div>
		<div class="item">
		<img class="third-slide" src="./image/head-3.jpg" alt="Third slide">
		<div class="container">
		<div class="carousel-caption">
		</div>
		</div>
		</div>
		</div>
		<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
		</a>
	</div>

	<div class="container marketing">

		<div class="page-header">
			<h2>公益活跃者</h2>
		</div>
		<div class="row">

		<div class="col-lg-4">
		<img class="img-rounded" src="./image/heart.jpg" alt="Generic placeholder image" width="140" height="140">
		<h3>活跃者用户名</h3>
		<p class="text-info">总捐助金额:￥1000</p>
		<p><a class="btn btn-default" href="#" role="button">了解更多 &raquo;</a></p>
		</div>

		<div class="col-lg-4">
		<img class="img-rounded" src="./image/heart.jpg" alt="Generic placeholder image" width="140" height="140">
		<h3>活跃者用户名</h3>
		<p class="text-info">总捐助金额:￥1000</p>
		<p><a class="btn btn-default" href="#" role="button">了解更多 &raquo;</a></p>
		</div>
		<div class="col-lg-4">
		<img class="img-rounded" src="./image/heart.jpg" alt="Generic placeholder image" width="140" height="140">
		<h3>活跃者用户名</h3>
		<p class="text-info">总捐助金额:￥1000</p>
		<p><a class="btn btn-default" href="#" role="button">了解更多 &raquo;</a></p>
		</div>
		<div class="col-lg-4">
		<img class="img-rounded" src="./image/heart.jpg" alt="Generic placeholder image" width="140" height="140">
		<h3>活跃者用户名</h3>
		<p class="text-info">总捐助金额:￥1000</p>
		<p><a class="btn btn-default" href="#" role="button">了解更多 &raquo;</a></p>
		</div>


		</div>

		<h2>公益活跃团体</h2>

		<hr class="featurette-divider">

		<div class="row featurette">

		<div class="col-md-7 col-md-push-5">
		<h3 class="featurette-heading">团体名称     <span class="text-muted">创建者：</span></h3>
		<p>寄语：</p>
		<p>故不登高山，不知天之高也；不临深溪，不知地之厚也；不闻先王之遗言，不知学问之大也。干、越、夷、貉之子，生而同声，长而异俗，教使之然也。诗曰：“嗟尔君子，无恒安息。靖共尔位，好是正直。神之听之，介尔景福。”神莫大于化道，福莫长于无祸。</p>
		</div>
		<div class="col-md-5 col-md-pull-7">
		<img class="featurette-image img-responsive center-block" src="./image/team.png" alt="Generic placeholder image">
		</div>

		<div class="col-md-7">
		<h3 class="featurette-heading">团体名称     <span class="text-muted">创建者：</span></h3>
		<p>寄语：</p>
		<p>故不登高山，不知天之高也；不临深溪，不知地之厚也；不闻先王之遗言，不知学问之大也。干、越、夷、貉之子，生而同声，长而异俗，教使之然也。诗曰：“嗟尔君子，无恒安息。靖共尔位，好是正直。神之听之，介尔景福。”神莫大于化道，福莫长于无祸。</p>
		</div>
		<div class="col-md-5">
		<img class="featurette-image img-responsive center-block" src="./image/team.png" alt="Generic placeholder image">
		</div>

		</div>
	</div>

<?php else: ?>

	<h1>所有社区</h1>
	<div class="row placeholders">
		<?php foreach($models as $community): ?>
		<div class="col-xs-6 col-sm-3 placeholder"><a href=<?=Url::to(['community/index','id'=>$community->community_id]) ?>>
		<img src="./image/community.jpg" width="200" height="200" class="img-circle" alt="Generic placeholder thumbnail">
		<h4><?=$community->community_name ?></h4>
		<span class="text-muted"><?=$community->getRegion() ?></span></a>
		</div>
		<?php endforeach; ?>

	</div>
	<div class="pagination pull-right"><?=LinkPager::widget(['pagination'=>$pager]) ?></div>

<?php endif ?>
