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
		Html::tag('div',
			$tracks[0]->album->active
				? Html::a(Html::icon('save').' PDF', ['albumpdf', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], ['class' => 'btn btn-xs btn-warning', 'style' => 'margin-top:25px;'])
				: Html::tag('span', 'unpublished', ['class' => 'badge', 'style' => 'margin-top:25px;'])
		, ['class' => 'pull-right'])
	, ['class' => 'clearfix']);

	$x = $y = 0;
	echo '<div class="row">';
	foreach($tracks as $track) :
		$y++;
		if ($x++ === 0)
			echo '<div class="col-sm-4 text-nowrap">';

		echo $track->track . ' · ';
		echo $track->lyricid ? Html::a($track->name, '#' . $track->track) : $track->name;
		echo '<br>';

		if ($x === (int) ceil(count($tracks) / 3) || $y === count($tracks)) {
			echo '</div>';
			$x = 0;
		}
	endforeach;
	echo '</div>';

	foreach($tracks as $track) :
		if ($track->lyricid) {
			echo Html::tag('div',
				Html::tag('div',
					Html::a(null, null, ['class' => 'anchor', 'name' => $track->track]) .
					Html::tag('h4', implode(' · ', [$track->track, $track->name])) .
					Html::tag('div', $track->lyrics->lyrics, ['class' => 'lyrics'])
				, ['class' => 'col-lg-12'])
			, ['class' => 'row']);
		}
	endforeach;
echo '</div>';
