<?php
use yii\helpers\Html;

$this->title = 'Lyrics';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));
?>
<div class="site-lyrics">
	<div class="row">
<?php
$x=0; $y=0;
foreach ($artists as $artist) :
	$x++; $y++;
	if ($x == 1) echo '<div class="col-sm-4 artists text-center text-nowrap">';
	echo Html::a($artist['artistName'], ['index', 'artist' => $artist['artistUrl']]);
	if ((int) $artist['active'] === 0) { echo ' <span class="badge">unpublished</span>'; }
	echo '<br />';

	if ($x == ceil(count($artists)/3) || $y == count($artists)) {
		echo '</div>';
		$x=0;
	}
endforeach;
?>
	</div>
</div>
