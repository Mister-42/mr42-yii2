<?php
namespace app\widgets;
use app\assets\LightboxAsset;
use yii\base\Widget;
use yii\helpers\{ArrayHelper, Html};
use yii\web\View;

class Lightbox extends Widget {
	public $items = [];
	public $options = [];
	public $linkOptions = ['class' => 'mr-2 my-1'];
	public $imageOptions = ['class' => 'd-none d-md-block rounded'];

	public function init() {
		LightboxAsset::register($this->getView());
		if(!empty($this->options))
			$this->getView()->registerJs('lightbox.option(' . json_encode($this->options) . ')', View::POS_END);
	}

	public function run() {
		$content = '';
		foreach ($this->items as $item) :
			if (!isset($item['thumb']) || !isset($item['image']))
				continue;

			$linkOptions['data-title'] = isset($item['title']) ? $item['title'] : '';
			$linkOptions['data-lightbox'] = isset($item['group']) ? $item['group'] : uniqid();
			$linkOptions = ArrayHelper::merge($linkOptions, $this->linkOptions);
			$image = Html::img($item['thumb'], $this->imageOptions);
			$content .= Html::a($image, $item['image'], $linkOptions);
		endforeach;

		return $content;
	}
}
