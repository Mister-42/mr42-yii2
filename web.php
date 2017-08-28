<?php
$secrets = require(__DIR__ . '/secrets.php');

$config = [
	'id' => 'mr42',
#	'catchAll' => ['site/offline'],
	'components' => [
		'authClientCollection' => [
			'class'   => \yii\authclient\Collection::className(),
			'clients' => [
				'facebook' => [
					'class'			=> 'Da\User\AuthClient\Facebook',
					'clientId'		=> $secrets['facebook']['Id'],
					'clientSecret'	=> $secrets['facebook']['Secret'],
				],
				'github' => [
					'class'			=> 'Da\User\AuthClient\GitHub',
					'clientId'		=> $secrets['github']['Id'],
					'clientSecret'	=> $secrets['github']['Secret'],
				],
				'google' => [
					'class'			=> 'Da\User\AuthClient\Google',
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
					'@Da/User/resources/views' => '@app/views/user'
				],
			],
		],
	],
	'modules' => [
		'user' => [
			'class' => Da\User\Module::class,
			'administrators' => ['admin'],
			'allowAccountDelete' => false,
			'controllerMap' => [
				'profile' => 'app\controllers\user\ProfileController',
			],
			'classMap' => [
				'Profile' => 'app\models\user\Profile',
				'RegistrationForm' => 'app\models\user\RegistrationForm',
			],
			'routes' => [
				'profile/<username:\w+>'					=> 'profile/show',
				'recenttracks/<username:\w+>'				=> 'profile/recenttracks',
				'<action:(login|logout)>'					=> 'security/<action>',
				'<action:(register|resend)>'                => 'registration/<action>',
				'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>'	=> 'registration/confirm',
				'forgot'									=> 'recovery/request',
				'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>'	=> 'recovery/reset',
				'settings/<action:\w+>'						=> 'settings/<action>',
			],
		],
	],
	'params' => require(__DIR__ . '/params.php'),
];

if (YII_DEBUG && in_array($_SERVER['REMOTE_ADDR'], $secrets['params']['specialIPs'])) {
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = [
		'class' => 'yii\debug\Module',
		'allowedIPs' => $secrets['params']['specialIPs'],
	];
}

if (YII_ENV_DEV && in_array($_SERVER['REMOTE_ADDR'], $secrets['params']['specialIPs'])) {
	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
		'allowedIPs' => $secrets['params']['specialIPs'],
	];
} else
	define('YII_ENV_DEV', false);

return $config;
