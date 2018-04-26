<?php
return [
	'cssCompressor' => 'sass {from} {to} --scss --sourcemap=none -C -t compressed -I '.Yii::getAlias('@bower/bootstrap/assets/stylesheets'),
	'deleteSource' => true,
	'bundles' => [
		'app\assets\AppAssetCompress'
	],
	'targets' => [
		'site' => [
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
				'scss' => ['css', 'sass {from} {to} -C --sourcemap=none -t compressed -I '.Yii::getAlias('@bower/bootstrap/scss')],
			],
		],
	],
];
