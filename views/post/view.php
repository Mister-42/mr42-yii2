<?php
use yii\helpers\Html;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>
<div class="articles-view">
	<div class="clearfix">
		<?php if ($olderLink = $model->olderLink): ?>
			<div class="pull-left"><?= $olderLink; ?></div>
		<?php endif; ?>
		<?php if ($newerLink = $model->newerLink): ?>
			<div class="pull-right"><?= $newerLink ?></div>
		<?php endif; ?>
	</div>

	<?php echo $this->render('_view', ['model' => $model, 'view' => 'full']); ?>

	<div class="comments"><?= Html::a(null, null, ['class' => 'anchor', 'id' => 'comments']) ?>
		<?php if(!empty($model->comments)) : ?>
			<hr />
			<h2>Comments</h2>

			<?= $this->render('_comments', ['mainmodel' => $model, 'model' => $comment, 'comments' => $model->comments]) ?>
		<?php endif; ?>

		<hr />
		<?= $this->render('_formComment', ['model' => $comment]) ?>
	</div>
</div>
