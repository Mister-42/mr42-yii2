<?php
use yii\bootstrap\Html;
?>
<bookmark content="track list" />

<br><br><br>
<div class="text-center">
	<?= Html::tag('h1', $tracks[0]->album->name, ['class' => 'text-center']) ?>
	by
	<?= Html::tag('h2', $tracks[0]->artist->name, ['class' => 'text-center']) ?>
</div>

<br><br><br>
<div class="col-sm-12 mpdf_toc" id="mpdf_toc_0">
	<?php foreach($tracks as $track) {
		echo '<div class="mpdf_toc_level_0">';

		echo '<a class="mpdf_toc_a" href="#'.$track->track.'">';
		echo '<span class="mpdf_toc_t_level_0">'.$track->name.'</span>';
		echo '</a>';

		echo '<dottab outdent="2em" />';

		echo '<a class="mpdf_toc_a" href="#'.$track->track.'">';
		echo '<span class="mpdf_toc_p_level_0">'.$track->track.'</span>';
		echo '</a>';

		echo '</div>';
	} ?>
</div>

<?php
foreach($tracks as $track) {
	echo '<pagebreak>';
	echo '<bookmark content="'.$track->name.'" />';
	echo Html::a(null, null, ['name' => $track->track]);
	echo Html::tag('h3', $track->name);

	if ($track->lyricid) {
		echo $track->lyrics->lyrics;
	} else {
		echo Html::img(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/TrebleClef.png');
		echo Html::tag('strong', 'Instrumental');
	}
}
?>
