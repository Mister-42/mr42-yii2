<?php
$config = [
	'id' => 'mr42-console',
	'aliases' => [
		'@web' => 'https://www.mr42.me/',
		'@webroot' => __DIR__ . '/../me.mr42.www',
	],
	'components' => [
		'urlManager' => [
			'baseUrl' => 'https://www.mr42.me/',
		],
	],
	'controllerNamespace' => 'app\commands',
];

return $config;
