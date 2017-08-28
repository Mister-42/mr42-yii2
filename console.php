<?php
return [
	'id' => 'mr42-console',
	'aliases' => [
		'@web' => 'https://www.mr42.me/',
		'@webroot' => __DIR__ . '/../webroot',
	],
	'components' => [
		'urlManager' => [
			'baseUrl' => 'https://www.mr42.me/',
		],
	],
	'controllerMap' => [
		'migrate' => [
			'class' => \yii\console\controllers\MigrateController::class,
			'migrationNamespaces' => [
				'Da\User\Migration',
			],
		],
	],
	'controllerNamespace' => 'app\commands',
];
