<?php

use mister42\widgets\Lightbox;
use yii\bootstrap4\Accordion;
use yii\bootstrap4\Html;
use yii\web\View;

$this->title = implode(' - ', [$album->artist->name, "{$album->name} ({$album->year})", Yii::t('mr42', 'Lyrics')]);
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Music')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('mr42', 'Lyrics'), 'url' => ['lyrics1artists']];
$this->params['breadcrumbs'][] = ['label' => Html::tag('span', $album->artist->name, ['class' => 'notranslate']), 'url' => ['lyrics2albums', 'artist' => $album->artist->url]];
$this->params['breadcrumbs'][] = Html::tag('span', "{$album->name} ({$album->year})", ['class' => 'notranslate']);

if ($album->image) {
    $this->registerMetaTag(['property' => 'og:image', 'content' => Yii::$app->mr42->createUrl(['music/albumcover', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, 'size' => 800], true)]);
}
$this->registerMetaTag(['property' => 'og:type', 'content' => 'music.album']);
$this->registerLinkTag(['rel' => 'alternate', 'href' => Yii::$app->mr42->createUrl(['music/albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], true), 'type' => 'application/pdf', 'title' => 'PDF']);
$this->registerJs(Yii::$app->formatter->jspack('accordionAnchor.js'), View::POS_READY);
$this->registerJs(Yii::$app->formatter->jspack('accordionScroll.js'), View::POS_READY);

if ($album->image_color) {
    $this->params['themeColor'] = $album->image_color;
}

$pdfUrl = Yii::$app->mr42->createUrl(['music/albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url]);
$items[] = [
    'label' => Html::tag('span', Html::tag('span', $album->year, ['class' => 'badge']) . Html::tag('span', implode(' - ', [$album->artist->name, $album->name]), ['class' => 'ml-1 notranslate']), ['class' => 'h4 float-left']),
    'content' => ($album->image)
        ? Lightbox::widget([
            'imageOptions' => ['class' => 'img-fluid img-thumbnail rounded', 'style' => ['background-color' => $album->image_color]],
            'items' => [
                [
                    'thumb' => Yii::$app->mr42->createUrl(['music/albumcover', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, 'size' => '500']),
                    'image' => Yii::$app->mr42->createUrl(['music/albumcover', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, 'size' => '800']),
                    'title' => implode(' - ', [$album->artist->name, $album->name]) . " ({$album->year})",
                ],
            ],
            'options' => [
                'imageFadeDuration' => 25,
                'wrapAround' => true,
            ],
        ])
        : null,
    'footer' => ($album->buy
            ? Html::a(Yii::$app->icon->name('bandcamp', 'brands')->class('mr-1') . Yii::t('mr42', 'Buy'), $album->buy, ['class' => 'btn btn-sm btn-outline-dark shadow ml-1', 'title' => Yii::t('mr42', 'Buy This Album')])
            : null) .
        ($album->playlist_url
            ? Html::a(Yii::$app->icon->name($album->playlist_source, 'brands')->class('mr-1') . Yii::t('mr42', 'Play'), $album->playlist_url, ['class' => 'btn btn-sm btn-outline-dark shadow ml-1'])
            : null) .
        ($album->active
            ? Html::a(Yii::$app->icon->name('file-pdf')->class('mr-1') . Yii::t('mr42', 'PDF'), $pdfUrl, ['class' => 'btn btn-sm btn-outline-dark shadow ml-1'])
            : Html::tag('span', Yii::t('mr42', 'Draft'), ['class' => 'btn btn-sm btn-warning disabled ml-1'])),
    'contentOptions' => ['class' => 'text-center'],
    'options' => ['id' => 'frontCover'],
];

foreach ($album->tracks as $track) {
    $content = ($track->video)
        ? Html::tag('div', $track->video, ['class' => ($track->instrumental) ? 'col-12' : 'col-12 col-md-4 order-md-12'])
        : null;

    $content .= Html::tag(
        'div',
        ($track->instrumental)
            ? Yii::$app->icon->name('@assetsroot/images/instrumental.svg')->class('img-fluid')->height(250)->title(Yii::t('mr42', 'Instrumental'))
            : ($track->lyricid ? $track->lyrics->lyrics : Html::tag('i', 'Work in Progress')),
        ['class' => $track->instrumental ? 'col-12 notranslate' : 'col-12 col-md-8 notranslate']
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
        'itemToggleOptions' => ['class' => 'text-left notranslate'],
    ]);
echo Html::endTag('div');
