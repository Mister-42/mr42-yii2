<?php
use yii\bootstrap4\Html;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

echo Html::beginTag('div', ['class' => 'clearfix']);
	if ($old = $model->find()->where(['<', 'id', $model->id])->orderBy('id DESC')->one())
		echo Html::a('&laquo; Previous Article', ['articles/index', 'id' => $old->id, 'title' => $old->url], ['class' => 'btn btn-sm btn-light float-left', 'data-toggle' => 'tooltip', 'data-placement' => 'right', 'title' => $old->title]);

	if ($new = $model->find()->where(['>', 'id', $model->id])->orderBy('id')->one())
		echo Html::a('Next Article &raquo;', ['articles/index', 'id' => $new->id, 'title' => $new->url], ['class' => 'btn btn-sm btn-light float-right', 'data-toggle' => 'tooltip', 'data-placement' => 'left', 'title' => $new->title]);
echo Html::endTag('div');

echo $this->render('_view', ['model' => $model, 'view' => 'full']);

echo Html::a(null, null, ['class' => 'anchor', 'id' => 'comments']);
if (!empty($model->comments)) {
	echo Html::tag('hr') . Html::tag('h3', 'Comments');
	echo $this->render('_comments', ['mainmodel' => $model, 'model' => $comment, 'comments' => $model->comments]);
}
echo Html::tag('hr') . $this->render('_formComment', ['model' => $comment]);
