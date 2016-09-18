<?php
use app\models\lyrics\Lyrics3Tracks;
use yii\bootstrap\Html;

$this->title = $albums[0]['artistName'];
$this->params['breadcrumbs'][] = ['label' => 'Lyrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $albums[0]['artistName'];

echo Html::tag('h1', Html::encode($albums[0]['artistName']));

foreach ($albums as $album) :
	echo '<div class="row">';
	echo '<div class="col-lg-12"><div class="clearfix"><div class="pull-left"><h3>' . $album['albumYear'] . ' · ';
	echo Html::a($album['albumName'], ['index', 'artist' => $album['artistUrl'], 'year' => $album['albumYear'], 'album' => $album['albumUrl']]);
	echo '</h3>';
	if ((int) $album['active'] === 0) { echo ' <span class="badge">unpublished</span>'; }
	echo '</div><div class="pull-right">';
	echo Html::a(Html::icon('save').' PDF', ['albumpdf', 'artist' => $album['artistUrl'], 'year' => $album['albumYear'], 'album' => $album['albumUrl']], ['class' => 'btn btn-xs btn-warning', 'style' => 'margin-top:25px;']);	
	echo '</div></div></div>';

	$x=0; $y=0;
	$tracks = Lyrics3Tracks::tracksList($albums[0]['artistUrl'], $album['albumYear'], $album['albumUrl']);
	foreach ($tracks as $track) :
		$x++; $y++;
		if ($x == 1)
			echo '<div class="col-sm-4 text-nowrap">';

		echo $track['trackNumber'] . ' · ';
		echo (strlen($track['trackLyrics']) === 0) ? $track['trackName'] : Html::a($track['trackName'], ['index', 'artist' => $album['artistUrl'], 'year' => $album['albumYear'], 'album' => $album['albumUrl'], '#' => $track['trackNumber']]);
		echo '<br />';

		if ($x == ceil(count($tracks) / 3) || $y == count($tracks)) {
			echo '</div>';
			$x=0;
		}
	endforeach;

	echo "</div>";
endforeach;
