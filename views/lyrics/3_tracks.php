<?php
use app\models\General;
use yii\bootstrap\Html;

$this->title = implode(' - ', [$tracks[0]['artistName'], $tracks[0]['albumName']]);
$this->params['breadcrumbs'][] = ['label' => 'Lyrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $tracks[0]['artistName'], 'url' => ['index', 'artist' => $tracks[0]['artistUrl']]];
$this->params['breadcrumbs'][] = $tracks[0]['albumName'];
?>
<div class="site-lyrics-lyrics">
	<div class="clearfix">
		<div class="pull-left">
			<?= Html::tag('h1', Html::encode(implode(' · ', [$tracks[0]['artistName'], $tracks[0]['albumName']]))) ?>
		</div>
		<div class="pull-right">
			<?= Html::a(Html::icon('save').' PDF', ['albumpdf', 'artist' => $tracks[0]['artistUrl'], 'year' => $tracks[0]['albumYear'], 'album' => $tracks[0]['albumUrl']], ['class' => 'btn btn-xs btn-warning', 'style' => 'margin-top:25px;']) ?>
		</div>
	</div>

	<?php
	$x=0; $y=0;
	echo '<div class="row">';
	foreach($tracks as $track) :
		$x++; $y++;
		if ($x == 1)
			echo '<div class="col-sm-4 text-nowrap">';

		echo $track['trackNumber'] . ' · ';
		echo (strlen($track['trackLyrics']) === 0) ? $track['trackName'] : Html::a($track['trackName'], '#' . $track['trackNumber']);
		echo '<br />';

		if ($x == ceil(count($tracks)/3) || $y == count($tracks)) {
			echo '</div>';
			$x=0;
		}
	endforeach;
	echo '</div>';

	foreach($tracks as $track) :
		if (strlen($track['trackLyrics']) !== 0) {
			echo '<div class="row"><div class="col-lg-12">';
			echo Html::a(null, null, ['name' => $track['trackNumber']]);
			echo Html::tag('h4', implode(' · ', [$track['trackNumber'], $track['trackName']]));
			echo General::cleanInput($track['trackLyrics'], 'gfm-comment');
			echo '</div></div>';
		}
	endforeach;
echo '</div>';
