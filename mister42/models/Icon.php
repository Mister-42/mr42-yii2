<?php
namespace app\models;
use DOMDocument;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\{ArrayHelper, StringHelper};

class Icon {
	public function show(string $name, array $options = []): string {
		$classPrefix = ArrayHelper::remove($options, 'prefix', 'fas fa-');
		$style = $this->getStyle((explode(' ', $classPrefix))[0]);
		if (!file_exists(Yii::getAlias("@bower/fontawesome/svgs/{$style}/{$name}.svg")))
			return $this->show('question-circle', $options);

		$doc = new DOMDocument();
		$doc->load(Yii::getAlias("@bower/fontawesome/svgs/{$style}/{$name}.svg"));
		foreach ($doc->getElementsByTagName('svg') as $svg) :
			$svg->setAttribute('aria-hidden', 'true');
			list($width, $height) = StringHelper::explode($svg->getAttribute('viewBox'), ' ', function($e) { return ltrim($e, '0'); }, true);
			$svg->setAttribute('class', trim(implode(' ', ['fa', 'w-'.ceil($width / $height * 16), ArrayHelper::remove($options, 'class')])));
			$svg->setAttribute('data-icon', $name);
			$svg->setAttribute('data-prefix', (explode(' ', $classPrefix))[0]);
			$svg->setAttribute('role', 'img');
			foreach ($options as $key => $value)
				$svg->setAttribute($key, $value);
		endforeach;
		foreach ($doc->getElementsByTagName('path') as $path)
			$path->setAttribute('fill', 'currentColor');
		return $doc->saveXML($doc->documentElement);
	}

	public function fieldAddon(string $name, array $options = []): string {
		$icon = Html::tag('div', $this->show($name, $options), ['class' => 'input-group-text']);
		return Html::tag('div', $icon, ['class' => 'input-group-prepend']);
	}

	private function getStyle(string $prefix): string {
		switch ($prefix) :
			case 'fab':
				return 'brands';
			case 'far':
				return 'regular';
			default:
				return 'solid';
		endswitch;
	}
}
