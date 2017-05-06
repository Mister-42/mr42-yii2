<?php
use yii\bootstrap\Html;

$this->title = implode(' - ', [$tracks[0]->artist->name, $tracks[0]->album->name, 'Lyrics']);
$this->params['breadcrumbs'][] = ['label' => 'Lyrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $tracks[0]->artist->name, 'url' => ['index', 'artist' => $tracks[0]->artist->url]];
$this->params['breadcrumbs'][] = $tracks[0]->album->name;

echo '<div class="site-lyrics-lyrics">';
	echo Html::tag('div',
		Html::tag('div',
			Html::tag('h1', Html::encode(implode(' · ', [$tracks[0]->artist->name, $tracks[0]->album->name])))
		, ['class' => 'pull-left']) .
		Html::tag('div', $tracks[0]->album->active
			? Html::a(Html::icon('save').' PDF', ['albumpdf', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], ['class' => 'btn btn-xs btn-warning action'])
			: Html::tag('span', 'Lyrics not available yet', ['class' => 'badge action'])
		, ['class' => 'pull-right']) .
		Html::tag('div', $tracks[0]->album->playlist_url
			? Html::a(Html::icon('play').' Play', $tracks[0]->album->playlist_url, ['class' => 'btn btn-xs btn-warning action']) . '&nbsp;'
			: ''
		, ['class' => 'pull-right'])
	, ['class' => 'clearfix']);

	$x = $y = 0;
	echo '<div class="row">';
	foreach($tracks as $track) :
		$y++;
		if ($x++ === 0)
			echo '<div class="col-sm-4 text-nowrap">';

		echo $track->track . ' · ';
		echo $track->hasLyrics || $track->video
			? Html::a($track->name, '#' . $track->track)
			: $track->name;
		echo $track->disambiguation . $track->feat;
		if ($track->video)
			echo ' ' . Html::icon($track->hasLyrics ? 'facetime-video' : 'fullscreen', ['class' => 'text-muted']);
		echo '<br>';

		if ($x === (int) ceil(count($tracks) / 3) || $y === count($tracks)) {
			echo '</div>';
			$x = 0;
		}
	endforeach;
	echo '</div>';

	if ($tracks[0]->album->image)
		echo Html::tag('div',
			Html::tag('div',
				Html::img(['cover', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url, 'size' => 500], ['alt' => implode(' · ', [$tracks[0]->artist->name, $tracks[0]->album->name]), 'class' => 'center-block img-responsive', 'height' => 500, 'width' => 500])
			, ['class' => 'col-xs-12'])
		, ['class' => 'row']);

	foreach($tracks as $track) :
		if ($track->lyricid || $track->video) {
			echo Html::tag('div',
				Html::tag('div',
					Html::a(null, null, ['class' => 'anchor', 'id' => $track->track]) .
					Html::tag('h4', implode(' · ', [$track->track, $track->name . $track->disambiguation . $track->feat])) .
					($track->lyricid ? Html::tag('div', $track->lyrics->lyrics, ['class' => 'lyrics']) : '')
				, ['class' => $track->lyricid ? 'col-xs-12 col-sm-8' : 'col-sm-12']) .
				Html::tag('div',
					$track->video
				, ['class' => $track->lyricid ? 'col-xs-12 col-sm-4' : 'col-sm-12'])
			, ['class' => 'row']);
		}
	endforeach;
echo '</div>';
