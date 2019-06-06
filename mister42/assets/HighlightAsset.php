<?php
namespace app\assets;
use yii\helpers\Json;
use yii\web\{AssetBundle, View};

class HighlightAsset extends AssetBundle {
	const DEFAULT_SELECTOR = 'pre code';

	public static $options = [];
	public static $selector = self::DEFAULT_SELECTOR;
	public $sourcePath = '@npm/highlightjs/';
 
	public $js = [
		'highlight.pack.min.js',
	];

	public $css = [
		'styles/default.css',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	public static function register($view) {
		$options = empty(self::$options) ? '' : Json::encode(self::$options);

		$view->registerJs("hljs.configure('{$options}');", View::POS_END);
		$view->registerJs(
			(self::$selector !== self::DEFAULT_SELECTOR)
				? "jQuery('{self::$selector}').each(function(i, block) {hljs.highlightBlock(block);});"
				: "hljs.initHighlightingOnLoad();"
		, View::POS_END);

		return parent::register($view);
	}
}
