<?php
use yii\bootstrap\Html;

$this->title = 'Lyrics';
$this->params['breadcrumbs'][] = $this->title;

echo '<div class="clearfix">';
	echo '<div class="pull-left">';
		echo Html::tag('h1', Html::encode($this->title));
	echo '</div>';
echo '</div>';

echo '<div class="site-lyrics">';
    echo '<div class="row">';
		$x = $y = 0;
		foreach ($artists as $artist) :
			$y++;
			if ($x++ === 0)
				echo '<div class="col-sm-3 artists text-center text-nowrap">';
			echo Html::a($artist->name, ['index', 'artist' => $artist->url]);
			if (!$artist->active)
				echo ' ' . Html::tag('span', 'Draft', ['class' => 'badge']);
			echo '<br>';

			if ($x === (int) ceil(count($artists) / 4) || $y === count($artists)) {
				echo '</div>';
				$x = 0;
			}
		endforeach;
	echo '</div>';
echo '</div>';
