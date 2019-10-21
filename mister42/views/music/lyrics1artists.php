<?php

use yii\bootstrap4\Html;

$this->title = Yii::t('mr42', 'Lyrics');
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Music')];
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'site-lyrics-artists']);
    echo Html::beginTag('div', ['class' => 'row text-center notranslate']);
    foreach (array_chunk($data, ceil(count($data) / 4)) as $artists) {
        echo Html::beginTag('div', ['class' => 'col-md-3 list-group']);
        foreach ($artists as $artist) {
            echo Html::a($artist->name, ['lyrics', 'artist' => $artist->url], ['class' => ['list-group-item', ($artist->active) ? 'list-group-item-action' : 'list-group-item-warning']]);
        }
        echo Html::endTag('div');
    }
    echo Html::endTag('div');
echo Html::endTag('div');
