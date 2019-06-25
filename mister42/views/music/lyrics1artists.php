<?php

use yii\bootstrap4\Html;

$this->title = Yii::t('mr42', 'Lyrics');
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Music')];
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'site-lyrics']);
    echo Html::beginTag('div', ['class' => 'row artists']);
    foreach (array_chunk($data, ceil(count($data) / 4)) as $artists) {
        echo Html::beginTag('div', ['class' => 'col-md-3 text-center text-nowrap']);
        foreach ($artists as $artist) {
            $draft = ($artist->active) ? '' : Html::tag('sup', Yii::t('mr42', 'Draft'), ['class' => 'badge badge-pill badge-warning ml-1']);
            echo Html::a($artist->name . $draft, ['lyrics', 'artist' => $artist->url], ['class' => 'notranslate']);
        }
        echo Html::endTag('div');
    }
    echo Html::endTag('div');
echo Html::endTag('div');
