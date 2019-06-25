<?php

use app\widgets\Item;
use app\widgets\WeeklyArtistChart;
use yii\bootstrap4\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$this->beginContent('@app/views/layouts/main.php');
$this->registerJs('(function refresh(){$(\'aside .tracks\').load(' . Json::htmlEncode(Url::to(['/user/recenttracks/' . basename(Url::current())])) . ');setTimeout(refresh,60000)})();');

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::tag('div', $content, ['class' => 'col-12 col-lg-8']);

    echo Html::tag(
        'aside',
        Html::tag('div', null, ['class' => 'tracks']) .
        Html::tag(
            'div',
            Item::widget([
                'body' => WeeklyArtistChart::widget(['profile' => basename(Url::current())]),
                'header' => Yii::$app->icon->name('lastfm-square', 'brands')->class('mr-1') . Yii::t('mr42', 'Weekly Artist Chart'),
            ])
        ),
        ['class' => 'd-none d-lg-inline col-4']
    );
echo Html::endTag('div');

$this->endContent();
