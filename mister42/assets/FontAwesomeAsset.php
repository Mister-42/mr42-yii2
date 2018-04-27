<?php
namespace app\assets;

class FontAwesomeAsset extends \yii\web\AssetBundle {
	public $sourcePath = '@bower/fontawesome/svg-with-js/js';

	public $js = [
		'fa-solid' . (YII_DEBUG ? '' : '.min') . '.js',
		'fa-brands' . (YII_DEBUG ? '' : '.min') . '.js',
		'fontawesome' . (YII_DEBUG ? '' : '.min') . '.js',
	];
}