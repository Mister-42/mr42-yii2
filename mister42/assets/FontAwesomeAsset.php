<?php
namespace app\assets;

class FontAwesomeAsset extends \yii\web\AssetBundle {
	public $sourcePath = '@bower/fontawesome/svg-with-js/js';

	public $js = [
		'fontawesome-all' . (YII_DEBUG ? '' : '.min') . '.js',
	];
}
