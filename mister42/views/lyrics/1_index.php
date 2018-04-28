<?php
use yii\bootstrap4\Html;

$this->title = 'Lyrics';
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

echo Html::beginTag('div', ['class' => 'site-lyrics']);
	echo Html::beginTag('div', ['class' => 'row artists']);
		$x = $y = 0;
		foreach ($data as $artist) :
			if ($x++ === 0)
				echo Html::beginTag('div', ['class' => 'col-md-3 text-center text-nowrap']);
			echo Html::a($artist->name, ['index', 'artist' => $artist->url]);
			if (!$artist->active)
				echo ' ' . Html::tag('span', 'Draft', ['class' => 'badge']);
			echo '<br>';

			if (++$y === count($data) || $x === (int) ceil(count($data) / 4)) {
				echo Html::endTag('div');
				$x = 0;
			}
		endforeach;
	echo Html::endTag('div');
echo Html::endTag('div');
