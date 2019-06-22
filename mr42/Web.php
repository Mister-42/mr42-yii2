<?php

namespace mr42;

class Web {
	public function getValues(): array {
		$config['id'] = 'mr42';
		$config['basePath'] = __DIR__;
		$config['components'] = $this->getComponents();
		return $config;
	}

	public function getComponents(): array {
		$params = (new \mister42\Params())->getValues();
		$secrets = (new \mister42\Secrets())->getValues();
		return [
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
				'baseUrl' => $params['longDomain'],
				'rules' => [
					'art<id:\d+>' => 'permalink/articles',
					'dl/php<version:\d+>' => 'download/php',
				],
			],
	];
	}
}
