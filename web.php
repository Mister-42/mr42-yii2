<?php
$secrets = require(__DIR__ . '/secrets.php');

$config = [
	'id' => 'mr42',
#	'catchAll' => ['site/offline'],
	'components' => [
		'authClientCollection' => [
			'class'   => \yii\authclient\Collection::className(),
			'clients' => [
				'github' => [
					'class'			=> 'dektrium\user\clients\GitHub',
					'clientId'		=> $secrets['github']['Id'],
					'clientSecret'	=> $secrets['github']['Secret'],
				],
				'google' => [
					'class'			=> 'dektrium\user\clients\Google',
					'clientId'		=> $secrets['google']['Id'],
					'clientSecret'	=> $secrets['google']['Secret'],
				],
			],
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'request' => [
			'cookieValidationKey' => $secrets['cookieValidationKey'],
		],
		'session' => [
			'class' => 'yii\web\DbSession',
			'sessionTable' => 'x_session',
		],
		'view' => [
			'theme' => [
				'pathMap' => [
					'@dektrium/user/views' => '@app/views/user'
				],
			],
		],
	],
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
			'admins' => ['admin'],
			'controllerMap' => [
				'profile' => 'app\controllers\user\ProfileController',
			],
			'modelMap' => [
				'Profile' => 'app\models\user\Profile',
				'RegistrationForm' => 'app\models\user\RegistrationForm',
			],
			'urlRules' => [
				'profile/<username:\w+>'					=> 'profile/show',
				'recenttracks/<username:\w+>'				=> 'profile/recenttracks',
				'<action:(login|logout)>'					=> 'security/<action>',
				'<action:(register|resend)>'				=> 'registration/<action>',
				'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>'	=> 'registration/confirm',
				'forgot'									=> 'recovery/request',
				'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>'	=> 'recovery/reset',
				'settings/<action:\w+>'						=> 'settings/<action>',
			],
		],
	],
	'params' => require(__DIR__ . '/params.php'),
];

if (YII_DEBUG && in_array($_SERVER['REMOTE_ADDR'], $config['params']['specialIPs'])) {
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = [
		'class' => 'yii\debug\Module',
		'allowedIPs' => $config['params']['specialIPs'],
	];
}

if (YII_ENV_DEV && in_array($_SERVER['REMOTE_ADDR'], $config['params']['specialIPs'])) {
	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
		'allowedIPs' => $config['params']['specialIPs'],
	];
} else
	define('YII_ENV_DEV', false);

return $config;
