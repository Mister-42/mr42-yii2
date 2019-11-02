<?php

use app\widgets\Lightbox;
use yii\bootstrap4\Html;

$this->title = implode(' ', [$data[0]->artist->name, 'Lyrics']);
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Music')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('mr42', 'Lyrics'), 'url' => ['lyrics']];
$this->params['breadcrumbs'][] = Html::tag('span', $data[0]->artist->name, ['class' => 'notranslate']);

echo Html::beginTag('div', ['class' => 'site-lyrics-albums']);
    echo Html::beginTag('div', ['class' => 'row']);
        echo Html::beginTag('div', ['class' => 'col']);
            echo Html::tag('h1', $data[0]->artist->name, ['class' => 'float-left']);
            echo Html::beginTag('div', ['class' => 'float-right']);
                if ($data[0]->artistInfo && $data[0]->artistInfo->buy) {
                    echo Html::a(Yii::$app->icon->name('bandcamp', 'brands'), $data[0]->artistInfo->buy, ['class' => 'btn btn-secondary ml-1', 'title' => Yii::t('mr42', 'Buy Music of {artist}', ['artist' => $data[0]->artist->name])]);
                }
                if ($data[0]->artistInfo && $data[0]->artistInfo->website) {
                    echo Html::a(Yii::$app->icon->name('globe'), $data[0]->artistInfo->website, ['class' => 'btn btn-secondary ml-1', 'title' => Yii::t('mr42', 'Website of {artist}', ['artist' => $data[0]->artist->name])]);
                }
            echo Html::endTag('div');
        echo Html::endTag('div');
    echo Html::endTag('div');

    if ($data[0]->artistInfo && $data[0]->artistInfo->bio_summary) {
        echo Html::beginTag('div', ['class' => 'row']);
        echo Html::tag('div', $data[0]->artistInfo->bioSummaryParsed, ['class' => 'col']);
        echo Html::endTag('div');
    }

    echo Html::beginTag('div', ['class' => 'container-fluid px-0']);
    foreach ($data as $album) {
        if ($album->tracks) {
            echo Html::beginTag('div', ['class' => ['card', $album === end($data) ? 'mb-1' : 'mb-3']]);
            echo Html::beginTag('div', ['class' => array_filter(['card-header', (!$album->active) ? 'bg-warning' : null])]);
            echo Html::tag('span', Html::tag('span', $album->year, ['class' => 'badge']) . Html::a($album->name, ['lyrics', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['class' => 'ml-1 notranslate']), ['class' => 'h4 float-left']);
            echo Html::beginTag('div', ['class' => 'float-right']);
            if ($album->buy) {
                echo Html::a(Yii::$app->icon->name('bandcamp', 'brands')->class('mr-1') . Yii::t('mr42', 'Buy'), $album->buy, ['class' => 'btn btn-sm btn-outline-dark ml-1', 'title' => Yii::t('mr42', 'Buy This Album')]);
            }
            if ($album->playlist_url) {
                echo Html::a(Yii::$app->icon->name($album->playlist_source, 'brands')->class('mr-1') . Yii::t('mr42', 'Play'), $album->playlist_url, ['class' => 'btn btn-sm btn-outline-dark ml-1', 'title' => Yii::t('mr42', 'Play Album')]);
            }
            echo Html::a(Yii::$app->icon->name('file-pdf')->class('mr-1') . Yii::t('mr42', 'PDF'), ['albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['class' => 'btn btn-sm btn-outline-dark ml-1', 'title' => Yii::t('mr42', 'PDF')]);
            echo Html::endTag('div');
            echo Html::endTag('div');

            echo Html::beginTag('div', ['class' => 'd-flex']);
            echo Html::beginTag('div', ['class' => 'row card-body notranslate']);
            foreach (array_chunk($album->tracks, ceil(count($album->tracks) / 3)) as $tracks) {
                echo Html::beginTag('div', ['class' => 'col-md-4 list-group']);
                foreach ($tracks as $track) {
                    echo Html::beginTag('div', ['class' => 'list-group-item list-group-item-action text-truncate py-0']);
                    echo Html::tag('span', $track->track, ['class' => 'badge']);
                    echo Html::a($track->name, ['lyrics', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, '#' => $track->track], ['class' => 'stretched-link ml-1']);
                    echo $track->nameExtra;
                    echo $track->icons;
                    echo Html::endTag('div');
                }
                echo Html::endTag('div');
            }
            echo Html::endTag('div');

            if ($album->image) {
                echo Lightbox::widget([
                    'imageOptions' => ['style' => ['background-color' => $album->image_color]],
                    'linkOptions' => ['class' => 'd-none d-md-block my-auto'],
                    'items' => [
                        [
                            'thumb' => ['albumcover', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, 'size' => '125'],
                            'image' => ['albumcover', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, 'size' => '800'],
                            'title' => implode(' - ', [$album->artist->name, $album->name]) . " ({$album->year})",
                            'group' => $album->artist->url,
                        ],
                    ],
                    'options' => [
                        'imageFadeDuration' => 25,
                        'wrapAround' => true,
                    ],
                ]);
            }
            echo Html::endTag('div');
            echo Html::endTag('div');
        }
    }
    echo Html::endTag('div');
echo Html::endTag('div');
