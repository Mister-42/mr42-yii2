<?php
use yii\helpers\Html;

$this->title = 'PHP ' . phpversion();
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

foreach (get_loaded_extensions() as $i => $ext)
	$modules[phpversion($ext)][] = $ext;

foreach($modules as $version => $items)
	$moduleList[] = Html::tag('tr', Html::tag('td', implode($items, ', ')) . Html::tag('td', $version));

echo Html::tag('table',
	Html::tag('tr',
		Html::tag('th', 'Name') .
		Html::tag('th', 'Version')
	) .
	implode($moduleList)
, ['border' => 1]);
