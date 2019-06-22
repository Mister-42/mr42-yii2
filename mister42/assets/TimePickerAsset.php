<?php

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class TimePickerAsset extends AssetBundle {
	public $sourcePath = '@bower/jqueryui-timepicker-addon/dist';

	public $depends = [
		'yii\jui\JuiAsset',
	];

	public $language;

	public function registerAssetFiles($view): void {
		$this->css[] = 'jquery-ui-timepicker-addon' . (YII_DEBUG ? '' : '.min') . '.css';
		$this->js[] = 'jquery-ui-timepicker-addon' . (YII_DEBUG ? '' : '.min') . '.js';

		$language = $this->language;

		if ($language !== null && $language !== 'en') {
			$fallbackLanguage = mb_substr($language, 0, 2);
			if ($fallbackLanguage !== $language && !file_exists(Yii::getAlias($this->sourcePath . "/i18n/jquery-ui-timepicker-{$language}.js"))) {
				$language = $fallbackLanguage;
			}
			$this->js[] = "i18n/jquery-ui-timepicker-{$language}.js";
		}

		parent::registerAssetFiles($view);
	}
}
