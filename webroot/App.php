<?php
namespace mr42;

class App {
	public function __construct(bool $debug = false) {
		if ($debug) :
			error_reporting(-1);
			define('YII_DEBUG', true);
		endif;

		header_remove("X-Powered-By");
		require(__DIR__.'/../../vendor/autoload.php');
		require(__DIR__.'/../../vendor/yiisoft/yii2/Yii.php');

		$app = new \yii\web\Application($this->getConfig());
		$app->run();
	}

	private function getConfig() {
		switch (\yii\helpers\ArrayHelper::getValue($_SERVER, 'SERVER_NAME')) :
			case 'mister42.me':
				return $this->loadConfig(['mister42']);
			default:
				return $this->loadConfig(['mister42', 'mr42']);
		endswitch;
	}

	private function loadConfig(array $dir): array {
		$webConfig = $dir[1] ?? $dir[0];
		return \yii\helpers\ArrayHelper::merge(
			require(__DIR__.'/../'.$dir[0].'/common.php'),
			require(__DIR__.'/../'.$webConfig.'/web.php')
		);
	}
}
