<?php

use app\widgets\Lightbox;
use yii\bootstrap4\Accordion;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = implode(' - ', [$data[0]->artist->name, "{$data[0]->album->name} ({$data[0]->album->year})", 'Lyrics']);
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Music')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('mr42', 'Lyrics'), 'url' => ['lyrics']];
$this->params['breadcrumbs'][] = ['label' => Html::tag('span', $data[0]->artist->name, ['class' => 'notranslate']), 'url' => ['lyrics', 'artist' => $data[0]->artist->url]];
$this->params['breadcrumbs'][] = Html::tag('span', "{$data[0]->album->name} ({$data[0]->album->year})", ['class' => 'notranslate']);

if ($data[0]->album->image) {
    $this->registerMetaTag(['property' => 'og:image', 'content' => Url::to(['albumcover', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url, 'size' => 800], true)]);
}
$this->registerMetaTag(['property' => 'og:type', 'content' => 'music.album']);
$this->registerLinkTag(['rel' => 'alternate', 'href' => Url::to(['albumpdf', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url], true), 'type' => 'application/pdf', 'title' => 'PDF']);
$this->registerJs(Yii::$app->formatter->jspack('accordionAnchor.js'), View::POS_READY);
$this->registerJs(Yii::$app->formatter->jspack('accordionScroll.js'), View::POS_READY);

if ($data[0]->album->image_color) {
    $this->params['themeColor'] = $data[0]->album->image_color;
}

$items[] = [
    'label' => Html::tag('span', Html::tag('span', $data[0]->album->year, ['class' => 'badge']) . Html::tag('span', implode(' - ', [$data[0]->artist->name, $data[0]->album->name]), ['class' => 'ml-1 notranslate']), ['class' => 'h4 float-left']),
    'content' => ($data[0]->album->image)
        ? Lightbox::widget([
            'imageOptions' => ['class' => 'img-fluid img-thumbnail rounded', 'style' => ['background-color' => $data[0]->album->image_color]],
            'items' => [
                [
                    'thumb' => ['albumcover', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url, 'size' => '500'],
                    'image' => ['albumcover', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url, 'size' => '800'],
                    'title' => implode(' - ', [$data[0]->artist->name, $data[0]->album->name]) . " ({$data[0]->album->year})",
                ],
            ],
            'options' => [
                'imageFadeDuration' => 25,
                'wrapAround' => true,
            ],
        ])
        : null,
    'footer' => ($data[0]->album->buy
            ? Html::a(Yii::$app->icon->name('bandcamp', 'brands')->class('mr-1') . Yii::t('mr42', 'Buy'), $data[0]->album->buy, ['class' => 'btn btn-sm btn-outline-dark ml-1', 'title' => Yii::t('mr42', 'Buy This Album')])
            : null) .
        ($data[0]->album->playlist_url
            ? Html::a(Yii::$app->icon->name($data[0]->album->playlist_source, 'brands')->class('mr-1') . Yii::t('mr42', 'Play'), $data[0]->album->playlist_url, ['class' => 'btn btn-sm btn-outline-dark ml-1'])
            : null) .
        ($data[0]->album->active
            ? Html::a(Yii::$app->icon->name('file-pdf')->class('mr-1') . Yii::t('mr42', 'PDF'), ['albumpdf', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url], ['class' => 'btn btn-sm btn-outline-dark ml-1'])
            : Html::tag('span', Yii::t('mr42', 'Draft'), ['class' => 'btn btn-sm btn-warning disabled ml-1'])),
    'contentOptions' => ['class' => 'text-center'],
    'options' => ['id' => 'frontCover'],
];

foreach ($data as $track) {
    $content = ($track->video)
        ? Html::tag('div', $track->video, ['class' => ($track->lyricid || $track->wip) ? 'col-12 col-md-4 order-md-12' : 'col-12'])
        : null;

    $content .= Html::tag(
        'div',
        ($track->lyricid || $track->wip || $track->video)
            ? ($track->wip) ? Html::tag('i', 'Work in Progress') : ($track->lyricid ? $track->lyrics->lyrics : '')
            : Yii::$app->icon->name('@assetsroot/images/instrumental.svg')->class('img-fluid')->height(250)->title(Yii::t('mr42', 'Instrumental')),
        ['class' => $track->lyricid || $track->wip ? 'col-12 col-md-8 notranslate' : 'col-12 notranslate']
    );

    $items[] = [
        'label' => Html::tag('span', $track->track, ['class' => 'badge']) . Html::tag('span', $track->name . $track->nameExtra . $track->icons, ['class' => 'ml-1']),
        'content' => Html::tag('div', $content, ['class' => 'row container']),
        'options' => ['id' => $track->track],
    ];
}

echo Html::beginTag('div', ['class' => 'site-lyrics-lyrics']);
    echo Accordion::widget([
        'encodeLabels' => false,
        'items' => $items,
        'itemToggleOptions' => ['class' => 'w-100 text-left notranslate'],
    ]);
echo Html::endTag('div');
