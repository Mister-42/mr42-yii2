<?php
$secrets = require(__DIR__.'/../mister42/secrets.php');

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
		'request' => [
			'cookieValidationKey' => $secrets['cookieValidationKey'],
		],
		'urlManager' => [
			'rules' => [
				'dl/php<version:\d+>'		=> 'download/php',
			],
		],
	],
	'id' => 'mr42',
];
