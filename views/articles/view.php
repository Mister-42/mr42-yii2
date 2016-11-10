<?php
use yii\bootstrap\Html;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>
<div class="articles-view">
	<div class="clearfix"><?php
		if ($older = $model->find()->where(['<', 'id', $model->id])->orderBy('id desc')->one())
			echo Html::a('&laquo; Previous Article', ['articles/index', 'id' => $older->id, 'title' => $older->url], ['class' => 'btn btn-sm btn-default pull-left', 'data-toggle' => 'tooltip', 'data-placement' => 'right', 'title' => Html::encode($older->title)]);
		if ($newer = $model->find()->where(['>', 'id', $model->id])->orderBy('id asc')->one())
			echo Html::a('Next Article &raquo;', ['articles/index', 'id' => $newer->id, 'title' => $newer->url], ['class' => 'btn btn-sm btn-default pull-right', 'data-toggle' => 'tooltip', 'data-placement' => 'left', 'title' => Html::encode($newer->title)]);
	?></div>

	<?= $this->render('_view', ['model' => $model, 'view' => 'full']) ?>

	<div class="comments"><?php
		echo Html::a(null, null, ['class' => 'anchor', 'id' => 'comments']);
		if (!empty($model->comments)) {
			echo '<hr>' . Html::tag('h2', 'Comments');
			echo $this->render('_comments', ['mainmodel' => $model, 'model' => $comment, 'comments' => $model->comments]);
		}
		echo '<hr>' . $this->render('_formComment', ['model' => $comment]);
	?></div>
</div>
