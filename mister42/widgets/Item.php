<?php
namespace app\widgets;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

class Item extends \yii\bootstrap\Widget {
	public $header;
	public $body;
	public $options;

	public function run(): string {
		$class[] = 'item';
		foreach ($this->options as $k => $v) :
			if ($k === 'class') {
				$class[] = $v;
				continue;
			}
			$option[] = ['name' => $k, 'value' => $v];
		endforeach;

		return Html::tag('div',
			(($this->header) ? Html::tag('div', $this->header, ['class' => 'item-heading']) : '') .
			Html::tag('div', $this->body, ['class' => 'item-body'])
		, ArrayHelper::merge(['class' => implode(' ', $class)], ArrayHelper::map($option, 'name', 'value')));
	}
}
