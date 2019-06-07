<?php
use yii\bootstrap4\Html;
use yii\helpers\Inflector;

$this->title = 'PHP '.phpversion().' &amp; '.'Yii '.Yii::getVersion();
$this->params['breadcrumbs'] = [$this->title];

echo Html::tag('h1', 'PHP '.phpversion());

foreach (get_loaded_extensions() as $i => $ext)
	$modules[phpversion($ext)][] = $ext;
$modules[INTL_ICU_VERSION][] = 'ICU';
$modules[INTL_ICU_DATA_VERSION][] = 'ICU Data';

echo Html::beginTag('div', ['class' => 'site-php-version']);
	foreach ($modules as $version => $items)
		echo Html::tag('div',
			Html::tag('div', Inflector::sentence($items, ' &amp; '), ['class' => 'col']).
			Html::tag('div', $version, ['class' => 'col-auto text-right'])
		, ['class' => 'row']);

	echo Html::tag('div', 'Yii '.Yii::getVersion(), ['class' => 'h1 mt-3']);

	foreach ($this->context->module->extensions as $data) :
		echo Html::beginTag('div', ['class' => 'row']);
			echo Html::tag('div', $data['name'], ['class' => 'col']);
			echo Html::beginTag('div', ['class' => 'col-auto text-nowrap']);
				foreach ($data['alias'] as $alias => $path) :
					echo Html::tag('div',
						Html::tag('div', $alias, ['class' => 'col w-50 text-right']).
						Html::tag('div', str_replace(realpath(Yii::getAlias('@vendor')).'/', '', $path), ['class' => 'col w-50'])
					, ['class' => 'row']);
				endforeach;
			echo Html::endTag('div');
			echo Html::tag('div', $data['version'], ['class' => 'col text-right']);
		echo Html::endTag('div');
	endforeach;
echo Html::endTag('div');
