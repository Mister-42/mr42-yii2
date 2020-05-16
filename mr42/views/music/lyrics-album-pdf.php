<?php

use mister42\models\music\Lyrics2Albums;
use yii\bootstrap4\Html;

echo '<bookmark content="tracklist" />';

echo '<br><br><br>';
echo '<div class="text-center">';
    echo $tracks[0]->album->image
        ? Html::img('data:image/jpeg;base64,' . base64_encode((Lyrics2Albums::getCover(500, $tracks))[1]), ['height' => 500, 'width' => 500])
        : Html::tag('h1', $tracks[0]->album->name) . PHP_EOL . 'by' . PHP_EOL . Html::tag('h2', $tracks[0]->artist->name);
echo '</div>';

echo '<br><br><br>';
echo '<div class="col-sm-12 mpdf_toc" id="mpdf_toc_0">';
    foreach ($tracks as $track) {
        echo '<div class="mpdf_toc_level_0">';

        echo '<a class="mpdf_toc_a" href="#' . $track->track . '">';
        echo '<span class="mpdf_toc_t_level_0">' . $track->name . $track->nameExtra . '</span>';
        echo '</a>';

        echo '<dottab outdent="2em" />';

        echo '<a class="mpdf_toc_a" href="#' . $track->track . '">';
        echo '<span class="mpdf_toc_p_level_0">' . $track->track . '</span>';
        echo '</a>';

        echo '</div>';
    }
echo '</div>';

foreach ($tracks as $track) {
    echo '<pagebreak>';
    echo '<bookmark content="' . $track->name . $track->nameExtra . '" />';
    echo Html::a(null, null, ['name' => $track->track]);
    echo Html::tag('h3', $track->name . $track->nameExtra);

    if ($track->lyricid && !$track->wip) {
        echo $track->lyrics->lyrics;
    } elseif ($track->wip) {
        echo Html::tag('i', 'Work in Progress');
    } else {
        echo Yii::$app->icon->name('@assetsroot/images/instrumental.svg')->class('img-fluid')->height(250)->title(Yii::t('mr42', 'Instrumental'));
    }
}
