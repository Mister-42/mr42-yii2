<?php
namespace mr42;

class Web {
	public function getValues(): array {
		$params = (new \mister42\Params())->getValues();
		return [
			'basePath' => __DIR__,
			'components' => [
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
					'cookieValidationKey' => $params['secrets']['cookieValidationKey'],
				],
				'urlManager' => [
					'rules' => [
						'dl/php<version:\d+>'		=> 'download/php',
					],
				],
			],
			'id' => 'mr42',
		];
	}
}
