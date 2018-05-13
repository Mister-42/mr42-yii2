<?php
namespace app\widgets;
use Yii;
use app\assets\TimePickerAsset;
use app\models\Icon;
use yii\base\InvalidConfigException;
use yii\bootstrap4\Html;
use yii\jui\{DatePicker, DatePickerLanguageAsset};

class TimePicker extends DatePicker {
	public $mode = 'datetime';
	public $addon = 'calendar';
	public $template = '{addon}{input}';
	public $size;

	public function init() {
		parent::init();
		if (!in_array($this->mode, ['date', 'time', 'datetime']))
			throw new InvalidConfigException('Unknown mode: "' . $this->mode . '". Use time, datetime or date!');

		if ($this->size) {
			Html::addCssClass($this->options, 'input-' . $this->size);
			Html::addCssClass($this->containerOptions, 'input-group-' . $this->size);
		}
		Html::addCssClass($this->options, 'form-control');
		Html::addCssClass($this->containerOptions, 'input-group ' . $this->mode);
	}

	public function run() {
		$this->clientOptions['showTime'] = $this->mode === 'date' ? false : true;

		if ($this->hasModel())
			$input = Html::activeTextInput($this->model, $this->attribute, $this->options);
		else
			$input = Html::textInput($this->name, $this->value, $this->options);

		if ($this->addon) {
			$input = strtr($this->template, ['{input}' => $input, '{addon}' => Icon::fieldAddon($this->addon)]);
			$input = Html::tag('div', $input, $this->containerOptions);
		}

		echo $input;
		$this->registerClientScript();
	}

	public function registerClientScript() {
		$view = $this->getView();
		$language = $this->language ? $this->language : Yii::$app->language;
		$name = $this->mode . 'picker';

		$timeAssetBundle = TimePickerAsset::register($view);
		if ($language !== 'en-US') {
			$timeAssetBundle->language = $language;
			$dateAssetBundle = DatePickerLanguageAsset::register($view);
			$dateAssetBundle->language = $language;
		}

		$this->registerClientOptions($name, $this->options['id']);
		$this->registerClientEvents($name, $this->options['id']);
	}
}