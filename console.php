<?php
$config = [
	'id' => 'mr42-console',
	'controllerNamespace' => 'app\commands',
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
		],
	],
];

return $config;
