<?php
namespace app\widgets;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class Item extends \yii\bootstrap4\Widget {
	public $header;
	public $body;
	public $options;

	public function run(): string {
		$class[] = 'card mb-2';
		foreach ($this->options as $k => $v) :
			if ($k === 'class') {
				$class[] = $v;
				continue;
			}
			$option[] = ['name' => $k, 'value' => $v];
		endforeach;

		return Html::tag('div',
			($this->header ? Html::tag('div', $this->header, ['class' => 'card-header']) : '').
			$this->body
		, ArrayHelper::merge(['class' => implode(' ', $class)], ArrayHelper::map($option, 'name', 'value')));
	}
}
