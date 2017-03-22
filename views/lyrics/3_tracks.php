<?php
use yii\bootstrap\Html;

$this->title = implode(' - ', [$tracks[0]->artist->name, $tracks[0]->album->name, 'Lyrics']);
$this->params['breadcrumbs'][] = ['label' => 'Lyrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $tracks[0]->artist->name, 'url' => ['index', 'artist' => $tracks[0]->artist->url]];
$this->params['breadcrumbs'][] = $tracks[0]->album->name;
?>
<div class="site-lyrics-lyrics">
	<div class="clearfix">
		<div class="pull-left">
			<?= Html::tag('h1', Html::encode(implode(' · ', [$tracks[0]->artist->name, $tracks[0]->album->name]))) ?>
		</div>
		<div class="pull-right">
			<?= Html::a(Html::icon('save').' PDF', ['albumpdf', 'artist' => $tracks[0]->artist->url, 'year' => $tracks[0]->album->year, 'album' => $tracks[0]->album->url], ['class' => 'btn btn-xs btn-warning', 'style' => 'margin-top:25px;']) ?>
		</div>
	</div>

	<?php
	$x = $y = 0;
	echo '<div class="row">';
	foreach($tracks as $track) :
		$y++;
		if ($x++ === 0)
			echo '<div class="col-sm-4 text-nowrap">';

		echo $track->track . ' · ';
		echo !$track->lyricid ? $track->name : Html::a($track->name, '#' . $track->track);
		echo '<br>';

		if ($x === (int) ceil(count($tracks) / 3) || $y === count($tracks)) {
			echo '</div>';
			$x = 0;
		}
	endforeach;
	echo '</div>';

	foreach($tracks as $track) :
		if ($track->lyricid) {
			echo '<div class="row"><div class="col-lg-12">';
			echo Html::a(null, null, ['class' => 'anchor', 'name' => $track->track]);
			echo Html::tag('h4', implode(' · ', [$track->track, $track->name]));
			echo Html::tag('div', $track->lyrics->lyrics, ['class' => 'lyrics']);
			echo '</div></div>';
		}
	endforeach;
echo '</div>';
