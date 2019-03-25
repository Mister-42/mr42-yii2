<?php
namespace mister42;

class Console {
	public function getValues(): array {
		return [
			'id' => 'mister42-console',
			'aliases' => [
				'@web' => 'https://www.mister42.me/',
				'@webroot' => __DIR__.'/../../webroot',
			],
			'basePath' => __DIR__,
			'components' => [
				'urlManager' => [
					'baseUrl' => 'https://www.mister42.me/',
				],
			],
			'controllerMap' => [
				'migrate' => [
					'class' => \yii\console\controllers\MigrateController::class,
					'migrationNamespaces' => [
						'Da\User\Migration',
					],
					'migrationPath' => [
						'@app/migrations',
						'@yii/rbac/migrations',
					],
				],
			],
			'controllerNamespace' => 'app\commands',
			'modules' => [
				'user' =>  \Da\User\Module::class,
			],
		];
	}
}
