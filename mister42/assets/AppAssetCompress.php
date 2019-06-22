<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAssetCompress extends AssetBundle {
	public $sourcePath = '@app/assets/css';

	public $css = [
		'site.scss',
	];

	public $js = [
	];
}
