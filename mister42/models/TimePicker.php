<?php
namespace app\models;
use Yii;
use app\assets\TimePickerAsset;
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
		if ($this->mode == 'date')
			$this->clientOptions['showTime'] = false;

		if ($this->inline === false) {
			if ($this->hasModel())
				$input = Html::activeTextInput($this->model, $this->attribute, $this->options);
			else
				$input = Html::textInput($this->name, $this->value, $this->options);

			if ($this->addon) {
				$addon = Icon::fieldAddon($this->addon);
				$input = strtr($this->template, ['{input}' => $input, '{addon}' => $addon]);
				$input = Html::tag('div', $input, $this->containerOptions);
			}
		} else {
			if ($this->hasModel()) {
				$input = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
				$attribute = $this->attribute;
				$this->clientOptions['defaultDate'] = $this->model->$attribute;
			} else {
				$input = Html::hiddenInput($this->name, $this->value, $this->options);
				$this->clientOptions['defaultDate'] = $this->value;
			}
			$this->clientOptions['altField'] = '#' . $this->options['id'];
			$this->clientOptions['altFieldTimeOnly'] = false;
			$input .= Html::tag('div', null, $this->containerOptions);
			$input = strtr($this->template, ['{input}' => $input, '{addon}' => '']);
		}

		echo $input;
		$this->registerClientScript();
	}

	public function registerClientScript() {
		$view = $this->getView();
		$containerID = $this->inline ? $this->containerOptions['id'] : $this->options['id'];
		$language = $this->language ? $this->language : Yii::$app->language;
		$name = $this->mode . 'picker';

		$timeAssetBundle = TimePickerAsset::register($view);
		if ($language !== 'en-US') {
			$timeAssetBundle->language = $language;
			$dateAssetBundle = DatePickerLanguageAsset::register($view);
			$dateAssetBundle->language = $language;
		}

		$this->registerClientOptions($name, $containerID);
		$this->registerClientEvents($name, $containerID);
	}
}
