<?php
namespace app\widgets;
use yii\bootstrap\Html;

class Item extends \yii\bootstrap\Widget {
	public $header;
	public $body;
	public $options;

	public function run() {
		$class[] = 'item';
		foreach ($this->options as $k => $v) :
			if ($k === 'class') {
				$class[] = $v;
				continue;
			}
			$option[] = "{$k}=\"{$v}\"";
		endforeach;

		echo '<div class="' . implode(' ', $class) . '" ' . implode(' ', $option) . '>';
		if ($this->header)
			echo Html::tag('div', $this->header, ['class' => 'item-heading']);
		echo Html::tag('div', $this->body, ['class' => 'item-body']);
		echo '</div>';
	}
}
