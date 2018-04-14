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
		foreach ($data as $artist) :
			$y++;
			if ($x++ === 0)
				echo '<div class="col-sm-3 artists text-center text-nowrap">';
			echo Html::a($artist->name, ['index', 'artist' => $artist->url]);
			if (!$artist->active)
				echo ' ' . Html::tag('span', 'Draft', ['class' => 'badge']);
			echo '<br>';

			if ($x === (int) ceil(count($data) / 4) || $y === count($data)) {
				echo '</div>';
				$x = 0;
			}
		endforeach;
	echo '</div>';
echo '</div>';
