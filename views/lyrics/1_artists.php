<?php
use yii\bootstrap\Html;

$this->title = 'Lyrics';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));
?>
<div class="site-lyrics">
	<div class="row">
<?php
$x = $y = 0;
foreach ($artists as $artist) :
	$y++;
	if ($x++ === 0) echo '<div class="col-sm-4 artists text-center text-nowrap">';
	echo Html::a($artist->name, ['index', 'artist' => $artist->url]);
	if (!$artist->active) { echo ' ' . Html::tag('span', 'unpublished', ['class' => 'badge']); }
	echo '<br />';

	if ($x == ceil(count($artists)/3) || $y == count($artists)) {
		echo '</div>';
		$x=0;
	}
endforeach;
?>
	</div>
</div>
