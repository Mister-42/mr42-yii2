<?php
use yii\bootstrap\Html;

$this->title = implode(' - ', [$data[0]->artist->name, $data[0]->album->name, 'Lyrics']);
$this->params['breadcrumbs'][] = ['label' => 'Lyrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $data[0]->artist->name, 'url' => ['index', 'artist' => $data[0]->artist->url]];
$this->params['breadcrumbs'][] = $data[0]->album->name;

echo '<div class="site-lyrics-lyrics">';
	echo Html::tag('div',
		Html::tag('div',
			Html::tag('h1', Html::encode(implode(' · ', [$data[0]->artist->name, $data[0]->album->name])))
		, ['class' => 'pull-left']) .
		Html::tag('div',
			($data[0]->album->playlist_url
				? Html::a(Html::icon('play').' Play', $data[0]->album->playlist_url, ['class' => 'btn btn-xs btn-warning action'])
				: '') .
			($data[0]->album->active
				? Html::a(Html::icon('save').' PDF', ['albumpdf', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url], ['class' => 'btn btn-xs btn-warning action'])
				: '')
		, ['class' => 'btn-toolbar pull-right'])
	, ['class' => 'clearfix']);

	$x = $y = 0;
	echo '<div class="row">';
	foreach($data as $track) :
		$y++;
		if ($x++ === 0)
			echo '<div class="col-sm-4 text-nowrap">';

		echo $track->track . ' · ';
		echo $track->lyricid || $track->video
			? Html::a($track->name, '#' . $track->track)
			: $track->name;
		echo $track->disambiguation . $track->feat;
		if ($track->video)
			echo ' ' . Html::icon($track->lyricid || $track->wip ? 'facetime-video' : 'fullscreen', ['class' => 'hidden-xs text-muted']);
		echo '<br>';

		if ($x === (int) ceil(count($data) / 3) || $y === count($data)) {
			echo '</div>';
			$x = 0;
		}
	endforeach;
	echo '</div>';

	if ($data[0]->album->image)
		echo Html::tag('div',
			Html::tag('div',
				Html::img(['albumcover', 'artist' => $data[0]->artist->url, 'year' => $data[0]->album->year, 'album' => $data[0]->album->url, 'size' => 500], ['alt' => implode(' · ', [$data[0]->artist->name, $data[0]->album->name]), 'class' => 'center-block img-responsive img-rounded', 'height' => 500, 'width' => 500])
			, ['class' => 'col-xs-12'])
		, ['class' => 'row']);

	foreach($data as $track) :
		if ($track->lyricid || $track->wip || $track->video)
			echo Html::tag('div',
				Html::tag('div',
					Html::a(null, null, ['class' => 'anchor', 'id' => $track->track]) .
					Html::tag('h4', implode(' · ', [$track->track, $track->name . $track->disambiguation . $track->feat])) .
					Html::tag('div', $track->wip ? Html::tag('i', 'Work in Progress') : ($track->lyricid ? $track->lyrics->lyrics : ''), ['class' => 'lyrics'])
				, ['class' => $track->lyricid || $track->wip ? 'col-xs-12 col-sm-8' : 'col-sm-12']) .
				Html::tag('div', $track->video, ['class' => $track->lyricid || $track->wip ? 'col-xs-12 col-sm-4' : 'col-xs-12'])
			, ['class' => 'row']);
	endforeach;
echo '</div>';
