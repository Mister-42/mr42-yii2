<?php
namespace mister42;

class Web {
	public function getValues(): array {
		$params = (new Params())->getValues();
		$config = [
			'id' => 'mister42',
		#	'catchAll' => in_array($_SERVER['REMOTE_ADDR'], $params['secrets']['params']['specialIPs']) ? null : ['site/offline'],
			'components' => [
				'authClientCollection' => [
					'class' => \yii\authclient\Collection::class,
					'clients' => [
						'facebook' => [
							'class'			=> 'Da\User\AuthClient\Facebook',
							'clientId'		=> $params['secrets']['facebook']['Id'],
							'clientSecret'	=> $params['secrets']['facebook']['Secret'],
						],
						'github' => [
							'class'			=> 'Da\User\AuthClient\GitHub',
							'clientId'		=> $params['secrets']['github']['Id'],
							'clientSecret'	=> $params['secrets']['github']['Secret'],
						],
						'google' => [
							'class'			=> 'Da\User\AuthClient\Google',
							'clientId'		=> $params['secrets']['google']['Id'],
							'clientSecret'	=> $params['secrets']['google']['Secret'],
						],
					],
				],
				'errorHandler' => [
					'errorAction' => 'site/error',
				],
				'log' => [
					'traceLevel' => YII_DEBUG ? 3 : 0,
					'targets' => [
						[
							'class' => 'yii\log\DbTarget',
							'except' => ['yii\web\HttpException:404'],
							'levels' => ['error'],
							'logTable' => 'log_mister42_error',
						],
					],
				],
				'reCaptcha' => [
					'name' => 'reCaptcha',
					'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
					'siteKey' => $params['secrets']['google']['reCAPTCHA']['siteKey'],
					'secret' => $params['secrets']['google']['reCAPTCHA']['secret'],
				],
				'request' => [
					'cookieValidationKey' => $params['secrets']['cookieValidationKey'],
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
					'class' => \Da\User\Module::class,
					'administrators' => ['admin'],
					'allowAccountDelete' => false,
					'classMap' => [
						'Profile' => 'app\models\user\Profile',
						'RegistrationForm' => 'app\models\user\RegistrationForm',
						'User' => 'app\models\user\User',
					],
					'controllerMap' => [
						'profile' => ['class' => 'app\controllers\user\ProfileController'],

					],
					'routes' => [
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
			'params' => $params,
		];

		if (YII_DEBUG && php_sapi_name() !== 'cli') :
			$config['bootstrap'] = ['debug'];
			$config['modules']['debug'] = [
				'class' => 'yii\debug\Module',
				'allowedIPs' => $params['secrets']['params']['specialIPs'],
			];
		endif;

		return $config;
	}
}
