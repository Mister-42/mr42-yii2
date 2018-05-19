<?php
header_remove("X-Powered-By");
#error_reporting(-1); defined('YII_DEBUG') or define('YII_DEBUG', true);

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

$domain = substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.' ? substr($_SERVER['HTTP_HOST'], 4) : $_SERVER['HTTP_HOST'];
switch ($domain) {
	case 'mister42.me': $cfg = getConfig(['mr42/mister42']); break;
	case 'mr42.me': $cfg = getConfig(['mr42/mister42', 'mr42/mr42']); break;
}

(new yii\web\Application($cfg))->run();

function getConfig(array $dir): array {
	$webConfig = $dir[1] ?? $dir[0];
	return \yii\helpers\ArrayHelper::merge(
		require(__DIR__ . '/../../' . $dir[0] . '/common.php'),
		require(__DIR__ . '/../../' . $webConfig . '/web.php')
	);
}
