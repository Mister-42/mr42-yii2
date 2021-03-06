<?php

namespace mister42\widgets;

use mister42\assets\LightboxAsset;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;

class Lightbox extends \yii\bootstrap4\Widget
{
    public $imageOptions;
    public $items;
    public $linkOptions;
    public $options;

    public function init(): void
    {
        LightboxAsset::register($this->getView());
        if (!empty($this->options)) {
            $this->getView()->registerJs('lightbox.option(' . json_encode($this->options) . ')', View::POS_END);
        }
    }

    public function run(): string
    {
        foreach ($this->items as $item) {
            if (!isset($item['thumb']) || !isset($item['image'])) {
                continue;
            }
            $imageOptions['alt'] = $item['title'] ?? '';
            $imageOptions['class'] = 'img-thumbnail';
            $imageOptions = ArrayHelper::merge($imageOptions, $this->imageOptions ?? []);
            $linkOptions['data-title'] = $item['title'] ?? '';
            $linkOptions['data-lightbox'] = $item['group'] ?? uniqid();
            $linkOptions = ArrayHelper::merge($linkOptions, $this->linkOptions ?? []);
            $content[] = Html::a(Html::img($item['thumb'], $imageOptions), $item['image'], $linkOptions);
        }

        return implode($content ?? []);
    }
}
