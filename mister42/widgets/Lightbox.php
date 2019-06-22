<?php

namespace app\widgets;

use app\assets\LightboxAsset;
use yii\bootstrap4\{Html, Widget};
use yii\helpers\ArrayHelper;
use yii\web\View;

class Lightbox extends Widget {
	public $imageOptions;
	public $items;
	public $linkOptions;
	public $options;

	public function init(): void {
		LightboxAsset::register($this->getView());
		if (!empty($this->options)) {
			$this->getView()->registerJs('lightbox.option(' . json_encode($this->options) . ')', View::POS_END);
		}
	}

	public function run(): string {
		foreach ($this->items as $item) {
			if (!isset($item['thumb']) || !isset($item['image'])) {
				continue;
			}
			$imageOptions['alt'] = $item['title'] ?? '';
			$imageOptions['class'] = 'd-none d-md-block img-thumbnail rounded';
			$imageOptions = ArrayHelper::merge($imageOptions, $this->imageOptions ?? []);
			$linkOptions['class'] = 'mr-2 my-1';
			$linkOptions['data-title'] = $item['title'] ?? '';
			$linkOptions['data-lightbox'] = $item['group'] ?? uniqid();
			$linkOptions = ArrayHelper::merge($linkOptions, $this->linkOptions ?? []);
			$content[] = Html::a(Html::img($item['thumb'], $imageOptions), $item['image'], $linkOptions);
		}

		return implode($content ?? []);
	}
}
