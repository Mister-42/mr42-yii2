<?php
$sass = 'sass --scss --sourcemap=none -C -t compressed -I '.Yii::getAlias('@npm/bootstrap/scss').' {from} {to}';

return [
	'bundles' => [
		'app\assets\AppAssetCompress'
	],
	'cssCompressor' => $sass,
	'deleteSource' => true,
	'targets' => [
		'all' => [
			'class' => 'yii\web\AssetBundle',
			'basePath' => '@runtime/assets',
			'baseUrl' => '@web/assets',
			'css' => 'css/site.css',
		],
	],
	'assetManager' => [
		'basePath' => '@runtime/assets',
		'baseUrl' => '@web/assets',
		'converter' => [
			'class' => 'yii\web\AssetConverter',
			'commands' => [
				'scss' => ['css', $sass],
			],
		],
	],
];
