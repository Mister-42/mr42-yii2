<?php
use yii\bootstrap4\Html;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

echo Html::beginTag('div', ['class' => 'clearfix mb-1']);
foreach (['<', '>'] as $z) :
	if ($art = $model->find()->where([$z, 'id', $model->id])->orderBy('id '.($z === '<' ? 'DESC' : 'ASC'))->one()) {
			echo Html::a(($z === '<' ? '&laquo; Previous Article' : 'Next Article &raquo;'), ['articles/index', 'id' => $art->id, 'title' => $art->url], ['class' => 'btn btn-sm btn-light float-'.($z === '<' ? 'left' : 'right'), 'data-toggle' => 'tooltip', 'data-placement' => ($z === '<' ? 'right' : 'left'), 'title' => $art->title]);
	}
	endforeach;
echo Html::endTag('div');

echo $this->render('_view', ['model' => $model, 'view' => 'full']);

echo Html::a(null, null, ['class' => 'anchor', 'id' => 'comments']);
if (!empty($model->comments)) {
	echo Html::tag('hr').Html::tag('h3', 'Comments');
	echo $this->render('_comments', ['mainmodel' => $model, 'model' => $comment, 'comments' => $model->comments]);
}
echo Html::tag('hr').$this->render('_formComment', ['model' => $comment]);
