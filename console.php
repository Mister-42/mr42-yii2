<?php
$config = [
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
	'controllerNamespace' => 'app\commands',
];

return $config;
