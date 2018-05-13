<?php
use yii\helpers\{Html, Inflector};

$this->title = 'PHP '.phpversion();
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

foreach (get_loaded_extensions() as $i => $ext) {
	$modules[phpversion($ext)][] = $ext;
}

echo Html::beginTag('div', ['class' => 'site-php-version']);
	foreach ($modules as $version => $items) {
			echo Html::tag('div',
			Html::tag('div', Inflector::sentence($items, ' &amp; '), ['class' => 'col-8']).
			Html::tag('div', $version, ['class' => 'col-4 text-right'])
		, ['class' => 'row']);
	}
	echo Html::endTag('div');
