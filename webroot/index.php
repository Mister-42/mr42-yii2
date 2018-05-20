<?php
class Index {
	private $debug = 0;

	public function __construct() {
		if ($this->debug) :
			error_reporting(-1);
			define('YII_DEBUG', true);
		endif;

		header_remove("X-Powered-By");
		require(__DIR__.'/../../vendor/autoload.php');
		require(__DIR__.'/../../vendor/yiisoft/yii2/Yii.php');
	}

	public function getConfig() {
		switch (substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.' ? substr($_SERVER['HTTP_HOST'], 4) : $_SERVER['HTTP_HOST']) :
			case 'mister42.me': return $this->loadConfig(['mister42']);
			case 'mr42.me': return $this->loadConfig(['mister42', 'mr42']);
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

$index = new Index();
$config = $index->getConfig();

$run = new yii\web\Application($config);
$run->run();
