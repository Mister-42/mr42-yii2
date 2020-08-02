<?php

use yii\helpers\ArrayHelper;

class App
{
    public function __construct(int $debug = 0, string $unitTest = null)
    {
        header_remove('X-Powered-By');
        if ($debug !== 0) {
            error_reporting(-1);
            define('YII_DEBUG', true);
            if ($debug === 2) {
                define('YII_ENV', 'dev');
            }
            define('YII_ENABLE_ERROR_HANDLER', !$unitTest);
        }

        require __DIR__ . '/vendor/autoload.php';
        require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

        $app = (PHP_SAPI === 'cli' && is_null($unitTest))
            ? new yii\console\Application($this->loadConfig(['mister42'], 'Console'))
            : new yii\web\Application($this->getConfig($unitTest));

        if ($unitTest) {
            $components = $app->components;
            $components['urlManager']['baseUrl'] = 'https://www.mister42.eu/';
            $app->components = $components;
            return $app;
        }

        return $app->run();
    }

    private function getConfig(?string $unitTest): array
    {
        return (ArrayHelper::getValue($_SERVER, 'SERVER_NAME') === 'mr42.me')
            ? $this->loadConfig(['mister42', 'mr42'])
            : $this->loadConfig(['mister42', $unitTest]);
    }

    private function loadConfig(array $dir, string $confFile = 'Web'): array
    {
        $config = ($dir[1] ?? $dir[0]) . "\\{$confFile}";
        $common = "{$dir[0]}\\Common";

        return yii\helpers\ArrayHelper::merge(
            (new $config())->getValues(),
            (new $common())->getValues()
        );
    }
}
