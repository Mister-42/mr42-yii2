<?php

namespace app\assets;

use yii\web\{AssetBundle, View};

class HighlightAsset extends AssetBundle {
	public $sourcePath = '@npm/highlightjs/';

	public $js = [
		'highlight.pack' . (YII_DEBUG ? '' : '.min') . '.js',
	];

	public $css = [
		'styles/default.css',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	public static function register($view) {
		$view->registerJs('hljs.initHighlightingOnLoad();', View::POS_END);

		return parent::register($view);
	}
}
