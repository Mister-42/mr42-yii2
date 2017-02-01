<?php
use yii\bootstrap\Carousel;
use yii\helpers\{FileHelper, Html, Url};

$this->title = 'PHP ' . phpversion();
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

foreach (get_loaded_extensions() as $i => $ext)
	$modules[] = Html::tag('tr', Html::tag('td', $ext) . Html::tag('td', phpversion($ext)));

echo Html::tag('table',
	Html::tag('tr',
		Html::tag('th', 'Name') .
		Html::tag('th', 'Version')
	) .
	implode($modules)
);
