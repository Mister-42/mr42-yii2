<?php
$secrets = require(__DIR__ . '/../mister42/secrets.php');

return [
	'id' => 'mr42',
	'basePath' => __DIR__,
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host='.$secrets['MySQL']['host'].';dbname='.$secrets['MySQL']['db'],
			'username' => $secrets['MySQL']['user'],
			'password' => $secrets['MySQL']['pass'],
			'charset' => 'utf8',
			'tablePrefix' => 'mr42_',
		],
		'errorHandler' => [
			'errorAction' => 'site/index',
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'normalizer' => [
				'class' => 'yii\web\UrlNormalizer',
			],
			'showScriptName' => false,
			'rules' => [
#				''																	=> 'site/index',
				'art<id:\d+>'														=> 'permalink/articles',
				'articles/<id:\d+>/<title:.*?>'										=> 'articles/index',
			],
		],
	],
	'name' => 'Mr.42',
	'runtimePath' => __DIR__ . '/../../../.cache/yii/mr42/mr42',
	'vendorPath' => __DIR__ . '/../../vendor',
];
