<?php
namespace app\models;
use DOMDocument;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\{ArrayHelper, StringHelper};

class Icon {
	public function instrumental(int $size, array $options = []): string {
		$options['height'] = $size;
		$options['width'] = $size;
		return $this->getSvg('instrumental', Yii::getAlias('@assetsroot/images/instrumental.svg'), $options);
	}

	public function show(string $name, array $options = []): string {
		$style = ArrayHelper::remove($options, 'style', 'solid');
		if (!file_exists(Yii::getAlias("@bower/fontawesome/svgs/{$style}/{$name}.svg")))
			return $this->show('question-circle', $options);
		return $this->getSvg($name, Yii::getAlias("@bower/fontawesome/svgs/{$style}/{$name}.svg"), $options);
	}

	public function fieldAddon(string $name, array $options = []): string {
		Html::addCssClass($options, 'icon-fw');
		$icon = Html::tag('div', $this->show($name, $options), ['class' => 'input-group-text']);
		return Html::tag('div', $icon, ['class' => 'input-group-prepend']);
	}

	private function getSvg(string $name, string $fileName, array $options): string {
		$target = ArrayHelper::remove($options, 'target', 'html');

		$doc = new DOMDocument();
		$doc->load($fileName);
		foreach ($doc->getElementsByTagName('svg') as $svg) :
			$svg->setAttribute('aria-hidden', 'true');
			if (min($options['height'], $options['width']) === null) :
				list($width, $height) = StringHelper::explode($svg->getAttribute('viewBox'), ' ', function($e) { return ltrim($e, '0'); }, true);
				$svg->setAttribute('class', trim(implode(' ', ['icon', 'icon-w-'.ceil($width / $height * 16), ArrayHelper::remove($options, 'class')])));
			endif;
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
}
