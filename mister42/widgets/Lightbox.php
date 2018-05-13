<?php
namespace app\widgets;
use app\assets\LightboxAsset;
use yii\base\Widget;
use yii\helpers\{ArrayHelper, Html};
use yii\web\View;

class Lightbox extends Widget {
	public $imageOptions = [];
	public $items = [];
	public $linkOptions = [];
	public $options = [];

	public function init() {
		LightboxAsset::register($this->getView());
		if (!empty($this->options)) :
			$this->getView()->registerJs('lightbox.option('.json_encode($this->options).')', View::POS_END);
		endif;
	}

	public function run() {
		foreach ($this->items as $item) :
			if (!isset($item['thumb']) || !isset($item['image'])) :
				continue;
			endif;

			$imageOptions['alt'] = isset($item['title']) ? $item['title'] : '';
			$imageOptions['class'] = 'd-none d-md-block img-thumbnail rounded';
			$linkOptions['class'] = 'mr-2 my-1';
			$linkOptions['data-title'] = isset($item['title']) ? $item['title'] : '';
			$linkOptions['data-lightbox'] = isset($item['group']) ? $item['group'] : uniqid();
			$imageOptions = ArrayHelper::merge($imageOptions, $this->imageOptions);
			$linkOptions = ArrayHelper::merge($linkOptions, $this->linkOptions);
			$image = Html::img($item['thumb'], $imageOptions);
			$content[] = Html::a($image, $item['image'], $linkOptions);
		endforeach;

		return implode($content);
	}
}
