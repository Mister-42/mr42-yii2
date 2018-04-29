<?php
namespace app\models;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class Icon {
	public static function show(string $name, array $options = []): string {
		$tag = ArrayHelper::remove($options, 'tag', 'span');
		$classPrefix = ArrayHelper::remove($options, 'prefix', 'fas fa-');
		Html::addCssClass($options, $classPrefix . $name);
		return Html::tag($tag, '', $options);
	}

	public static function fieldAddon(string $name, array $options = []): string {
		$icon = Html::tag('div', static::show($name, $options), ['class' => 'input-group-text']);
		return Html::tag('div', $icon, ['class' => 'input-group-prepend']);
	}
}
