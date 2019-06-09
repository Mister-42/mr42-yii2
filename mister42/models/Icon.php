<?php
namespace app\models;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class Icon {
	public function fieldAddon(string $name, array $options = []): string {
		Html::addCssClass($options, 'icon-fw');
		$icon = Html::tag('div', $this->show($name, $options), ['class' => 'input-group-text']);
		return Html::tag('div', $icon, ['class' => 'input-group-prepend']);
	}

	public function inputTemplate(string $name, array $options = []): string {
		return Html::tag('div', $this->fieldAddon($name, $options).'{input}', ['class' => 'input-group']);
	}

	public function instrumental(array $options = []): string {
		$svg = Image::loadSvg('@assetsroot/images/instrumental.svg');
		ArrayHelper::setValue($options, 'title', 'Instrumental');
		return Image::processSvg($svg, $options);
	}

	public function show(string $name, array $options = []): string {
		$style = ArrayHelper::remove($options, 'style', 'solid');
		$svg = Image::loadSvg("@bower/fontawesome/svgs/{$style}/{$name}.svg");
		return Image::processSvg($svg, $options);
	}
}
