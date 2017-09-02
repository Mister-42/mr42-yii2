<?php
return [
	'basePath' => __DIR__,
	'components' => [
		'errorHandler' => [
			'errorAction' => 'site/index',
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'except' => ['yii\web\HttpException:404'],
					'levels' => ['error'],
					'logFile' => '@runtime/logs/mr42.error.log',
				], [
					'class' => 'yii\log\FileTarget',
					'categories' => ['yii\web\HttpException:404'],
					'levels' => ['error', 'warning'],
					'logFile' => '@runtime/logs/mr42.404.log',
				],
			],
		],

	],
	'id' => 'mr42',
];
