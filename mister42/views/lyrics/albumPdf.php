<?php
use app\models\lyrics\Lyrics2Albums;
use yii\bootstrap\Html;
?>
<bookmark content="track list" />

<br><br><br>
<div class="text-center"><?php
	echo $tracks[0]->album->image
		? Html::img('data:image/jpeg;base64,'.base64_encode((Lyrics2Albums::getCover(500, $tracks))[1]), ['height' => 500, 'width' => 500])
		: Html::tag('h1', $tracks[0]->album->name) . PHP_EOL . 'by' . PHP_EOL . Html::tag('h2', $tracks[0]->artist->name);
?></div>

<br><br><br><?php
echo '<div class="col-sm-12 mpdf_toc" id="mpdf_toc_0">';
	foreach($tracks as $track) :
		echo '<div class="mpdf_toc_level_0">';

		echo '<a class="mpdf_toc_a" href="#'.$track->track.'">';
		echo '<span class="mpdf_toc_t_level_0">'.$track->name.$track->disambiguation.$track->feat.'</span>';
		echo '</a>';

		echo '<dottab outdent="2em" />';

		echo '<a class="mpdf_toc_a" href="#'.$track->track.'">';
		echo '<span class="mpdf_toc_p_level_0">'.$track->track.'</span>';
		echo '</a>';

		echo '</div>';
	endforeach;
echo '</div>';

foreach($tracks as $track) :
	echo '<pagebreak>';
	echo '<bookmark content="'.$track->name.$track->disambiguation.$track->feat.'" />';
	echo Html::a(null, null, ['name' => $track->track]);
	echo Html::tag('h3', $track->name . $track->disambiguation . $track->feat);

	if ($track->lyricid) {
		echo $track->lyrics->lyrics;
	} else {
		echo Html::img(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/TrebleClef.png');
		echo Html::tag('strong', 'Instrumental');
	}
endforeach;
