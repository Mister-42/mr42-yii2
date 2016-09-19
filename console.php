<?php
$config = [
	'id' => 'mr42-console',
	'components' => [
		'urlManager' => [
			'baseUrl' => 'https://www.mr42.me/',
		],
	],
	'controllerNamespace' => 'app\commands',
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
		],
	],
];

return $config;
