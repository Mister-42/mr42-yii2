<?php
namespace app\models;
use DOMDocument;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\{ArrayHelper, StringHelper};

class Icon {
	public function show(string $name, array $options = []): string {
		$style = ArrayHelper::remove($options, 'style', 'solid');
		$target = ArrayHelper::remove($options, 'target', 'html');
		if (!file_exists(Yii::getAlias("@bower/fontawesome/svgs/{$style}/{$name}.svg")) && $name !== 'instrumental')
			return $this->show('question-circle', $options);

		$doc = new DOMDocument();
		$doc->load($name === 'instrumental' ? Yii::getAlias('@assetsroot/images/instrumental.svg') : Yii::getAlias("@bower/fontawesome/svgs/{$style}/{$name}.svg"));
		foreach ($doc->getElementsByTagName('svg') as $svg) :
			$svg->setAttribute('aria-hidden', 'true');
			list($width, $height) = StringHelper::explode($svg->getAttribute('viewBox'), ' ', function($e) { return ltrim($e, '0'); }, true);
			if (!isset($options['height']) && !isset($options['width']))
				$svg->setAttribute('class', trim(implode(' ', ['icon', 'icon-w-'.ceil($width / $height * 16), ArrayHelper::remove($options, 'class')])));
			$svg->setAttribute('data-icon', $name);
			$svg->setAttribute('role', 'img');
			foreach ($options as $key => $value)
				$svg->setAttribute($key, $value);
		endforeach;
		if ($target !== 'pdf')
			foreach ($doc->getElementsByTagName('path') as $path)
				$path->setAttribute('fill', 'currentColor');
		return $doc->saveXML($doc->documentElement);
	}

	public function fieldAddon(string $name, array $options = []): string {
		Html::addCssClass($options, 'icon-fw');
		$icon = Html::tag('div', $this->show($name, $options), ['class' => 'input-group-text']);
		return Html::tag('div', $icon, ['class' => 'input-group-prepend']);
	}
}
