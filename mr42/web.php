<?php
return [
	'basePath' => __DIR__,
	'components' => [
		'errorHandler' => [
			'errorAction' => 'redirect/index',
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\DbTarget',
					'except' => ['yii\web\HttpException:404'],
					'levels' => ['error'],
					'logTable' => 'log_mr42_error',
				],
			],
		],
		'urlManager' => [
			'rules' => [
				'dl/php<version:\d+>'		=> 'download/php',
			],
		],
	],
	'id' => 'mr42',
];
