<?php
use yii\bootstrap\Html;

$this->title = implode(' - ', [$albums[0]->artist->name, 'Lyrics']);
$this->params['breadcrumbs'][] = ['label' => 'Lyrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $albums[0]->artist->name;

echo Html::tag('h1', Html::encode($albums[0]->artist->name));

foreach ($albums as $album) :
	echo '<div class="row">';
	echo Html::tag('div',
		Html::tag('div',
			Html::tag('div',
				Html::tag('h3', $album->year . ' · ' . Html::a($album->name, ['index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url]))
			, ['class' => 'pull-left']) .
			Html::tag('div',
				$album->active
					? Html::a(Html::icon('save').' PDF', ['albumpdf', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url], ['class' => 'btn btn-xs btn-warning action'])
					: Html::tag('span', 'Not published', ['class' => 'badge action'])
			, ['class' => 'pull-right'])
		, ['class' => 'clearfix'])
	, ['class' => 'col-lg-12']);

	$x = $y = 0;
	foreach ($album->tracks as $track) :
		$y++;
		if ($x++ === 0)
			echo '<div class="col-sm-4 text-nowrap">';

		$track->name = $track->lyricid
			? Html::a($track->name, ['index', 'artist' => $album->artist->url, 'year' => $album->year, 'album' => $album->url, '#' => $track->track])
			: $track->name;
		echo implode(' · ', [$track->track, $track->name]);
		echo '<br>';

		if ($x === (int) ceil(count($album->tracks) / 3) || $y === count($album->tracks)) {
			echo '</div>';
			$x = 0;
		}
	endforeach;

	echo "</div>";
endforeach;
