<?php

use yii\bootstrap4\Tabs;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('mr42', 'My Pi');
$this->params['breadcrumbs'] = [Yii::$app->name];
$this->params['breadcrumbs'][] = $this->title;

$tabs = [
    'day' => ['short' => Yii::t('mr42', 'Day'), 'long' => Yii::t('mr42', 'Last Day')],
    'week' => ['short' => Yii::t('mr42', 'Week'), 'long' => Yii::t('mr42', 'Last Week')],
    'month' => ['short' => Yii::t('mr42', 'Month'), 'long' => Yii::t('mr42', 'Last Month')],
    'year' => ['short' => Yii::t('mr42', 'Year'), 'long' => Yii::t('mr42', 'Last Year')],
];
$datatype = [
    'tempload' => Yii::t('mr42', 'Temperature and Load'),
    'storage' => Yii::t('mr42', 'Disk Space and Memory Usage'),
    'network' => Yii::t('mr42', 'Network Traffic'),
];
$hosts = ['pi-hole', 'jukebox'];

$this->registerJs(Yii::$app->formatter->jspack('jquery.unveil.js'), View::POS_END);
$this->registerJs('$(\'a[data-toggle="tab"]\').on(\'shown.bs.tab\', function (e) {$(window).trigger("lookup")})', View::POS_END);
$this->registerJs('$("img").unveil();', View::POS_READY);

echo Html::tag('h1', $this->title);

foreach ($tabs as $tab => $tabdesc) {
    $$tab[] = Html::beginTag('div', ['class' => 'row']);
    foreach ($datatype as $dt => $dtdesc) {
        $$tab[] = Html::tag('h4', $dtdesc, ['class' => 'w-100 text-center mt-2 mb-0']);
        foreach ($hosts as $host) {
            $$tab[] = Html::beginTag('div', ['class' => 'col-md-6 h-100']);
            $$tab[] = ($tab === array_key_first($tabs))
                ? Html::img("@assets/pi/{$tab}-{$host}-{$dt}.png", ['alt' => yii::t('mr42', '{desc} of {host}', ['desc' => $dtdesc, 'host' => $host]) . " ({$tabdesc['long']})", 'class' => 'img-fluid mb-2'])
                : Html::img('@assets/images/loading.png', ['alt' => Yii::t('mr42', '{desc} of {host}', ['desc' => $dtdesc, 'host' => $host]) . " ({$tabdesc['long']})", 'class' => 'img-fluid mb-2', 'data-src' => Yii::getAlias("@assets/pi/{$tab}-{$host}-{$dt}.png")]);
            $$tab[] = Html::endTag('div');
        }
    }
    $$tab[] = Html::endTag('div');

    $items[$tab]['label'] = $tabdesc['short'];
    $items[$tab]['content'] = implode($$tab);
    $items[$tab]['active'] = $tab === array_key_first($tabs);
}

echo Tabs::widget([
    'items' => $items,
]);
