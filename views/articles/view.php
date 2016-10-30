<?php
use yii\bootstrap\Html;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>
<div class="articles-view">
	<div class="clearfix"><?php
		if ($olderLink = $model->olderLink)
			echo $olderLink;
		if ($newerLink = $model->newerLink)
			echo $newerLink;
	?></div>

	<?= $this->render('_view', ['model' => $model, 'view' => 'full']) ?>

	<div class="comments"><?php
		echo Html::a(null, null, ['class' => 'anchor', 'id' => 'comments']);
		if(!empty($model->comments)) {
			echo '<hr>' . Html::tag('h2', 'Comments');
			echo $this->render('_comments', ['mainmodel' => $model, 'model' => $comment, 'comments' => $model->comments]);
		}
		echo '<hr>' . $this->render('_formComment', ['model' => $comment]); ?>
	</div>
</div>
