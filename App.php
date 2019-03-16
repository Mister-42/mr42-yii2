<?php
class App {
	public function __construct(bool $debug = false, bool $unitTest = false) {
		if ($debug) :
			error_reporting(-1);
			define('YII_DEBUG', true);
		endif;

		header_remove("X-Powered-By");
		require __DIR__.'/../vendor/autoload.php';
		require __DIR__.'/../vendor/yiisoft/yii2/Yii.php';

		if ($unitTest) :
			define('YII_ENABLE_ERROR_HANDLER', false);
			return new yii\web\Application($this->loadConfig(['mister42'], 'web'));
		endif;

		$app = (php_sapi_name() === 'cli')
			? new yii\console\Application($this->loadConfig(['mister42'], 'console'))
			: new yii\web\Application($this->getConfig());

		$exitCode = $app->run();
		exit($exitCode);
	}

	private function getConfig(): array {
		switch (yii\helpers\ArrayHelper::getValue($_SERVER, 'SERVER_NAME')) :
			case 'mister42.me':
				return $this->loadConfig(['mister42'], 'web');
			default:
				return $this->loadConfig(['mister42', 'mr42'], 'web');
		endswitch;
	}

	private function loadConfig(array $dir, string $confFile): array {
		$webConfig = $dir[1] ?? $dir[0];
		return yii\helpers\ArrayHelper::merge(
			require __DIR__."/{$dir[0]}/common.php",
			require __DIR__."/{$webConfig}/{$confFile}.php"
		);
	}
}
