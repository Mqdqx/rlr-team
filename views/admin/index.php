<?php

/* @var $this view:admin/index应用中心 */
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

$this->title = '应用中心-管理员';
$this->params['breadcrumbs'][] = $this->title;

$option = Yii::$app->request->get('option');
if ($option) {
	switch ($option) {
		case 'user':
			$query = \app\models\User::find();
			break;
		case 'wish':
			$query = \app\models\Wish::find()->where(['!=','status',0]);
			break;	
		case 'community':
			$query = \app\models\Community::find();
			break;
		case 'flows':
			$query = \app\models\Flows::find();
			break;
		default:
			throw new \Exception("Error Processing Request", 1);
			break;
	}
	$dataProvider = new ActiveDataProvider([
        'query'=>$query,
        'pagination' => [
            'pagesize' => 10
        ],
    ]);

}

?>

<div class="row">
	<!-- 引用渲染应用中心左侧导航 -->
	<?= $this->render('menu.php') ?>
	<div class="col-lg-10">
		<!-- 横向导航 -->
		<ul class="nav nav-tabs" id="nav_option">
			<li><a href=<?=Url::to(['admin/index','option'=>'user'])?>>用户</a></li>
			<li><a href=<?=Url::to(['admin/index','option'=>'wish'])?>>心愿</a></li>
			<li><a href=<?=Url::to(['admin/index','option'=>'community'])?>>社区</a></li>
			<li><a href=<?=Url::to(['admin/index','option'=>'flows'])?>>流水</a></li>
		</ul>
		<!-- 横向导航 -->
		<?php if (isset($dataProvider)): ?>
			<?php if ($option == 'user'): ?>
				<?=GridView::widget([
					'dataProvider'=>$dataProvider,
					'layout'=>"{items}\n{pager}",
					'emptyText'=>'当前无数据',
					'columns'=>[
						'user_id',
						'email',
						'username',
						'balance',
						'number',
						'alipay:text:收款号',
						'logintime:datetime:登录时间',
					],
				]) ?>
			<?php elseif($option == 'wish'): ?>
				<?=GridView::widget([
					'dataProvider'=>$dataProvider,
					'layout'=>"{items}\n{pager}",
					'emptyText'=>'当前无数据',
					'columns'=>[
						'wish_id',
					],
				]) ?>
			<?php elseif($option == 'community'): ?>

			<?php elseif($option == 'flows'): ?>

			<?php endif ?>
		<?php endif ?>
	</div>
</div>