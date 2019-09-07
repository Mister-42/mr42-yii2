<?php

namespace app\widgets;

use app\assets\TimePickerAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap4\Html;
use yii\jui\DatePicker;
use yii\jui\DatePickerLanguageAsset;

class TimePicker extends DatePicker
{
    public $addon = 'calendar';
    public $mode = 'datetime';
    public $size;
    public $template = '{addon}{input}';

    public function init(): void
    {
        parent::init();
        if (!in_array($this->mode, ['date', 'time', 'datetime'])) {
            throw new InvalidConfigException('Unknown mode: "' . $this->mode . '". Use time, datetime or date!');
        }
        if ($this->size) {
            Html::addCssClass($this->options, 'input-' . $this->size);
            Html::addCssClass($this->containerOptions, 'input-group-' . $this->size);
        }
        Html::addCssClass($this->options, 'form-control');
        Html::addCssClass($this->containerOptions, 'input-group ' . $this->mode);
    }

    public function registerClientScript(): void
    {
        $view = $this->getView();
        $language = $this->language ? $this->language : Yii::$app->language;
        $name = $this->mode . 'picker';

        $timeAssetBundle = TimePickerAsset::register($view);
        if ($language !== 'en') {
            $timeAssetBundle->language = $language;
            $dateAssetBundle = DatePickerLanguageAsset::register($view);
            $dateAssetBundle->language = $language;
        }

        $this->registerClientOptions($name, $this->options['id']);
        $this->registerClientEvents($name, $this->options['id']);
    }

    public function run(): void
    {
        $this->clientOptions['showTime'] = $this->mode !== 'date';

        $input = Html::textInput($this->name, $this->value, $this->options);
        if ($this->hasModel()) {
            $input = Html::activeTextInput($this->model, $this->attribute, $this->options);
        }

        if ($this->addon) {
            $input = strtr($this->template, ['{input}' => $input, '{addon}' => Yii::$app->icon->activeFieldIcon($this->addon)]);
            $input = Html::tag('div', $input, $this->containerOptions);
        }

        echo $input;
        $this->registerClientScript();
    }
}
