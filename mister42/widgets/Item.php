<?php

namespace app\widgets;

use yii\bootstrap4\{Html, Widget};
use yii\helpers\ArrayHelper;

class Item extends Widget {
	public $header;
	public $body;
	public $options;

	public function run(): string {
		$class[] = 'card mb-2';
		foreach ($this->options as $key => $value) {
			if ($key === 'class') {
				$class[] = $value;
				continue;
			}
			$option[] = ['name' => $key, 'value' => $value];
		}

		return Html::tag(
			'div',
			($this->header ? Html::tag('div', $this->header, ['class' => 'card-header']) : '') .
			$this->body,
			ArrayHelper::merge(['class' => implode(' ', $class)], ArrayHelper::map($option, 'name', 'value'))
		);
	}
}
