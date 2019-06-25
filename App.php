<?php

class App
{
    public function __construct(bool $debug = false, bool $unitTest = false)
    {
        header_remove('X-Powered-By');
        if ($debug) {
            error_reporting(-1);
            define('YII_DEBUG', true);
            define('YII_ENABLE_ERROR_HANDLER', !$unitTest);
        }

        $loader = require __DIR__ . '/../vendor/autoload.php';
        $loader->setPsr4('mr42\\', __DIR__ . '/mr42/');
        $loader->setPsr4('mister42\\', __DIR__ . '/mister42/');
        require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

        if ($unitTest) {
            return new yii\web\Application($this->loadConfig(['mister42'], 'Web'));
        }
        $app = (php_sapi_name() === 'cli')
            ? new yii\console\Application($this->loadConfig(['mister42'], 'Console'))
            : new yii\web\Application($this->getConfig());

        $exitCode = $app->run();
        return $exitCode;
    }

    private function getConfig(): array
    {
        switch (yii\helpers\ArrayHelper::getValue($_SERVER, 'SERVER_NAME')) :
            case 'mister42.me':
                return $this->loadConfig(['mister42'], 'Web');
        default:
                return $this->loadConfig(['mister42', 'mr42'], 'Web');
        endswitch;
    }

    private function loadConfig(array $dir, string $confFile): array
    {
        $config = ($dir[1] ?? $dir[0]) . "\\{$confFile}";
        $common = "{$dir[0]}\\Common";

        return yii\helpers\ArrayHelper::merge(
            (new $config())->getValues(),
            (new $common())->getValues()
        );
    }
}
