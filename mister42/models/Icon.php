<?php
namespace app\models;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\{ArrayHelper, StringHelper};

class Icon {
	public static function show(string $name, array $options = []): string {
		$classPrefix = ArrayHelper::remove($options, 'prefix', 'fas fa-');
		$style = (explode(' ', $classPrefix))[0] === 'fab' ? 'brands' : 'solid';
		if (!$fa = @file_get_contents(Yii::getAlias("@bower/fontawesome/advanced-options/raw-svg/{$style}/{$name}.svg")))
			return static::show('question-circle', $options);

		$svg = simplexml_load_string($fa, 'SimpleXMLElement');
		list($width, $height) = StringHelper::explode(str_replace('0', '', $svg->attributes()->viewBox), ' ', true, true);

		return Html::tag('svg',
			Html::tag('path', null, ['d' => $svg->path->attributes()->d, 'fill' => 'currentColor'])
		, [
			'aria-hidden' => 'true',
			'class' => trim(implode(' ', ['fa', 'w-'.ceil($width / $height * 16), ArrayHelper::getValue($options, 'class')])),
			'data-icon' => $name,
			'data-prefix' => (explode(' ', $classPrefix))[0],
			'role' => 'img',
			'viewbox' => $svg->attributes()->viewBox,
			'xmlns' => 'http://www.w3.org/2000/svg'
		]);
	}

	public static function fieldAddon(string $name, array $options = []): string {
		$icon = Html::tag('div', static::show($name, $options), ['class' => 'input-group-text']);
		return Html::tag('div', $icon, ['class' => 'input-group-prepend']);
	}
}
