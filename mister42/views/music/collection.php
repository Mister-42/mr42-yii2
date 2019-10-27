<?php

use app\models\music\Collection;
use yii\bootstrap4\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::t('mr42', 'Collection');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Music');
$this->params['breadcrumbs'][] = $this->title;

$tabs = [
    'collection' => Yii::t('mr42', 'Collection'),
    'wishlist' => Yii::t('mr42', 'Wishlist'),
];

$this->registerJs(Yii::$app->formatter->jspack('jquery.unveil.js'), View::POS_END);
$this->registerJs('$(\'a[data-toggle="tab"]\').on(\'shown.bs.tab\', function (e) {$(window).trigger("lookup")})', View::POS_END);
$this->registerJs('$("img").unveil();', View::POS_READY);

echo Html::tag('h1', $this->title);

foreach ($tabs as $tab => $tabdesc) {
    $$tab[] = Html::beginTag('div', ['class' => 'row justify-content-center']);
    foreach (Collection::find()->where(['user_id' => 1, 'status' => $tab])->orderBy(['artist' => SORT_ASC, 'year' => SORT_ASC])->all() as $album) {
        $$tab[] = Html::beginTag('div', ['class' => 'col-12 col-sm-6 col-md-3 col-xl-2 mt-3 d-sm-flex align-items-stretch']);
        $$tab[] = Html::beginTag('div', ['class' => 'card text-center notranslate']);
        $$tab[] = Html::tag('div', $album->artist, ['class' => 'card-header p-2']);
        $$tab[] = Html::tag('div', Html::tag('span', $album->title, ['class' => 'card-text mx-auto']), ['class' => 'card-body d-flex p-2']);
        $$tab[] = Html::a(
            Html::img('@assets/images/blank.png', ['alt' => "{$album->artist} - {$album->title} ({$album->year})", 'class' => 'card-img-bottom', 'data-src' => Url::to(['music/collection-cover', 'id' => $album->id])]),
            "https://www.discogs.com/release/{$album->id}"
        );
        $$tab[] = Html::endTag('div');
        $$tab[] = Html::endTag('div');
    }
    $$tab[] = Html::endTag('div');

    $items[$tab]['label'] = $tabdesc;
    $items[$tab]['content'] = implode($$tab);
    $items[$tab]['active'] = $tab === array_key_first($tabs);
}

echo Tabs::widget([
    'items' => $items,
]);
