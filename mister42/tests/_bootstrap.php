<?php
error_reporting(-1);
define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_DEBUG', true);

require(__DIR__.'/../../../vendor/autoload.php');
require(__DIR__.'/../../../vendor/yiisoft/yii2/Yii.php');

$config = yii\helpers\ArrayHelper::merge(
	require(__DIR__.'/../common.php'),
	require(__DIR__.'/../web.php')
);

(new yii\web\Application($config));
