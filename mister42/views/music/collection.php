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
        $$tab[] = Html::beginTag('div', ['class' => 'col-12 col-sm-6 col-md-3 col-xl-2 mt-3']);
        $$tab[] = Html::beginTag('div', ['class' => 'card']);
        $$tab[] = Html::a(
            Html::img('@assets/images/blank.png', ['alt' => "{$album->artist} - {$album->year} - {$album->title}", 'class' => 'card-img-top rounded', 'data-src' => Url::to(['music/collection-cover', 'id' => $album->id])]),
            "https://www.discogs.com/release/{$album->id}"
        );
        $$tab[] = Html::tag('div', Html::tag('small', $album->title, ['class' => 'card-text mt-auto mx-auto font-weight-bold notranslate']), ['class' => 'card-body d-flex text-center p-2']);
        $$tab[] = Html::tag('div', Html::tag('small', $album->artist), ['class' => 'card-footer text-center p-2 notranslate']);
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
