<?php
use app\models\lyrics\Lyrics3Tracks;
use yii\bootstrap\Html;

$this->title = $albums[0]->artist->name;
$this->params['breadcrumbs'][] = ['label' => 'Lyrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $albums[0]->artist->name;

echo Html::tag('h1', Html::encode($albums[0]->artist->name));

foreach ($albums as $album) :
	echo '<div class="row">';
	echo '<div class="col-lg-12"><div class="clearfix"><div class="pull-left"><h3>' . $album->year . ' · ';
	echo Html::a($album->name, ['index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url]);
	echo '</h3>';
	if (!$album->active) { echo ' ' . Html::tag('span', 'unpublished', ['class' => 'badge']); }
	echo '</div><div class="pull-right">';
	echo Html::a(Html::icon('save').' PDF', ['albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['class' => 'btn btn-xs btn-warning', 'style' => 'margin-top:25px;']);
	echo '</div></div></div>';

	$x = $y = 0;
	foreach ($album->tracks as $track) :
		$y++;
		if ($x++ === 0)
			echo '<div class="col-sm-4 text-nowrap">';

		echo $track->track . ' · ';
		echo (!$track->lyricid) ? $track->name : Html::a($track->name, ['index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, '#' => $track->track]);
		echo '<br />';

		if ($x == ceil(count($album->tracks) / 3) || $y == count($album->tracks)) {
			echo '</div>';
			$x=0;
		}
	endforeach;

	echo "</div>";
endforeach;
