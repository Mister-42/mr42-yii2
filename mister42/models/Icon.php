<?php
namespace app\models;
use DOMDocument;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\{ArrayHelper, StringHelper};

class Icon {
	public static function show(string $name, array $options = []): string {
		$classPrefix = ArrayHelper::remove($options, 'prefix', 'fas fa-');
		$style = (explode(' ', $classPrefix))[0] === 'fab' ? 'brands' : 'solid';
		if (!file_exists(Yii::getAlias("@bower/fontawesome/advanced-options/raw-svg/{$style}/{$name}.svg"))) :
			return static::show('question-circle', $options);
		endif;

		$doc = new DOMDocument();
		$doc->load(Yii::getAlias("@bower/fontawesome/advanced-options/raw-svg/{$style}/{$name}.svg"));
		foreach($doc->getElementsByTagName('svg') as $svg) :
			$svg->setAttribute('aria-hidden', 'true');
			list($width, $height) = StringHelper::explode($svg->getAttribute('viewBox'), ' ', function($e) { return ltrim($e, '0'); }, true);
			$svg->setAttribute('class', trim(implode(' ', ['fa', 'w-'.ceil($width / $height * 16), ArrayHelper::getValue($options, 'class')])));
			$svg->setAttribute('data-icon', $name);
			$svg->setAttribute('data-prefix', (explode(' ', $classPrefix))[0]);
			$svg->setAttribute('role', 'img');
		endforeach;
		foreach($doc->getElementsByTagName('path') as $path) :
			$path->setAttribute('fill', 'currentColor');
		endforeach;
		return $doc->saveXML($doc->documentElement);
	}

	public static function fieldAddon(string $name, array $options = []): string {
		$icon = Html::tag('div', static::show($name, $options), ['class' => 'input-group-text']);
		return Html::tag('div', $icon, ['class' => 'input-group-prepend']);
	}
}
