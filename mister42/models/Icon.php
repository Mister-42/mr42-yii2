<?php
namespace app\models;
use DOMDocument;
use DOMElement;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\{ArrayHelper, StringHelper};

class Icon {
	public function instrumental(int $height, array $options = []): string {
		$doc = $this->loadSvg('@assetsroot/images/instrumental.svg');
		[$options['width'], $options['height']] = $this->getSvgSize($doc->getElementsByTagName('svg')->item(0), $height);
		return $this->processSvg($doc, 'instrumental', $options);
	}

	public function show(string $name, array $options = []): string {
		$style = ArrayHelper::remove($options, 'style', 'solid');
		$svg = $this->loadSvg("@bower/fontawesome/svgs/{$style}/{$name}.svg");
		return $this->processSvg($svg, $name, $options);
	}

	public function fieldAddon(string $name, array $options = []): string {
		Html::addCssClass($options, 'icon-fw');
		$icon = Html::tag('div', $this->show($name, $options), ['class' => 'input-group-text']);
		return Html::tag('div', $icon, ['class' => 'input-group-prepend']);
	}

	private function loadSvg(string $fileName): DOMDocument {
		$doc = new DOMDocument();
		if (!file_exists(Yii::getAlias($fileName)))
			$fileName = '@bower/fontawesome/svgs/solid/question-circle.svg';

		$doc->load(Yii::getAlias($fileName));
		return $doc;
	}

	private function processSvg(DOMDocument $doc, string $name, array $options): string {
		$target = ArrayHelper::remove($options, 'target', 'html');
		foreach ($doc->getElementsByTagName('svg') as $svg) :
			$svg->setAttribute('aria-hidden', 'true');
			if (!array_key_exists('width', $options)) :
				[$width, $height] = $this->getSvgSize($svg);
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

	private function getSvgSize(DOMElement $element, int $height = 0): array {
		[$svgWidth, $svgHeight] = StringHelper::explode($element->getAttribute('viewBox'), ' ', function($e) { return ltrim($e, '0'); }, true);
		return ($height > 0)
			? [round($height * $svgWidth / $svgHeight), $height]
			: [round($svgWidth), round($svgHeight)];
	}
}
