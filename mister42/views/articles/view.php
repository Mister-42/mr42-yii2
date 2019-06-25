<?php

use app\models\articles\ArticlesComments;
use yii\bootstrap4\Html;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('mr42', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

echo Html::beginTag('div', ['class' => 'clearfix mb-2']);
foreach (['<', '>'] as $z) {
    if ($art = $model->find()->orderBy(['updated' => $z === '<' ? SORT_DESC : SORT_ASC])->where([$z, 'updated', $model->updated])->limit(1)->one()) {
        echo Html::a(($z === '<' ? '&laquo; ' . Yii::t('mr42', 'Previous Article') : Yii::t('mr42', 'Next Article') . ' &raquo;'), ['article', 'id' => $art->id, 'title' => $art->url], ['class' => 'btn btn-sm btn-outline-secondary float-' . ($z === '<' ? 'left' : 'right'), 'data-toggle' => 'tooltip', 'data-placement' => ($z === '<' ? 'right' : 'left'), 'title' => $art->title]);
    }
}
echo Html::endTag('div');

echo $this->render('_view', ['model' => $model]);

echo Html::a(null, null, ['class' => 'anchor', 'id' => 'comments']);
if (!empty($model->comments)) {
    echo Html::tag('h3', Yii::t('mr42', 'Comments'), ['class' => 'text-center']);
    echo $this->render('_comments', ['data' => $model]);
}

echo $this->render('_formComment', ['model' => new ArticlesComments()]);
