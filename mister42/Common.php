<?php

namespace mister42;

use yii\helpers\ArrayHelper;

class Common
{
    private array $params;
    private array $secrets;

    public function __construct()
    {
        $this->params = (new Params())->getValues();
        $this->secrets = (new Secrets())->getValues();
    }

    public function getValues(): array
    {
        return [
            'aliases' => [
                '@assets' => '//mr42.me',
                '@assetsroot' => __DIR__ . '/../assets',
                '@bower' => '@vendor/bower-asset',
                '@npm' => '@vendor/npm-asset',
                '@mister42' =>  '@app/../mister42',
                '@siteDE' => 'https://www.mister42.de',
                '@siteEN' => 'https://www.mister42.me',
                '@siteRU' => 'https://www.xn--42-mlclt0afi.xn--p1ai',
            ],
            'bootstrap' => ['log'],
            'components' => $this->getComponents(),
            'language' => $this->getDomainProperty('lang'),
            'name' => $this->getDomainProperty('title'),
            'params' => $this->params,
            'runtimePath' => __DIR__ . '/../.cache',
            'timeZone' => 'Europe/Berlin',
            'vendorPath' => __DIR__ . '/../vendor',
        ];
    }

    private function getComponents(): array
    {
        return [
            'assetManager' => [
                'basePath' => '@assetsroot',
                'baseUrl' => '@assets',
                'bundles' => [
                    'yii\bootstrap4\BootstrapAsset' => [
                        'css' => [],
                    ],
                    'yii\bootstrap4\BootstrapPluginAsset' => [
                        'js' => [YII_DEBUG ? 'js/bootstrap.bundle.js' : 'js/bootstrap.bundle.min.js'],
                    ],
                    'yii\jui\JuiAsset' => [
                        'css' => [YII_DEBUG ? 'themes/smoothness/jquery-ui.css' : 'themes/smoothness/jquery-ui.min.css'],
                        'js' => [YII_DEBUG ? 'jquery-ui.js' : 'jquery-ui.min.js'],
                    ],
                    'yii\web\JqueryAsset' => [
                        'js' => [YII_DEBUG ? 'jquery.js' : 'jquery.min.js'],
                    ],
                ],
                'linkAssets' => true,
            ],
            'authManager' => [
                'class' => 'yii\rbac\DbManager',
            ],
            'cache' => [
                'class' => 'yii\caching\DbCache',
            ],
            'fileCache' => [
                'class' => 'yii\caching\FileCache',
                'directoryLevel' => 0,
            ],
            'db' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=' . $this->secrets['MySQL']['host'] . ';dbname=' . $this->secrets['MySQL']['db'],
                'username' => $this->secrets['MySQL']['user'],
                'password' => $this->secrets['MySQL']['pass'],
                'charset' => 'utf8mb4',
                'tablePrefix' => 'mister42_',
                'enableSchemaCache' => true,
                'schemaCache' => 'fileCache',
                'schemaCacheDuration' => 60 * 60 * 24 * 7,
                'enableQueryCache' => true,
                'queryCache' => 'fileCache',
                'queryCacheDuration' => 60 * 60 * 24 * 2,
            ],
            'formatter' => [
                'class' => 'mister42\models\Formatter',
            ],
            'icon' => [
                'class' => 'thoulah\fontawesome\IconComponent',
                'prefix' => 'icon',
                'registerAssets' => false,
            ],
            'i18n' => [
                'translations' => [
                    '*' => [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'sourceLanguage' => 'en',
                    ],
                ],
            ],
            'mailer' => [
                'class' => 'yii\swiftmailer\Mailer',
            ],
            'urlManager' => [
                'class' => 'yii\web\UrlManager',
                'enablePrettyUrl' => true,
                'normalizer' => [
                    'class' => 'yii\web\UrlNormalizer',
                ],
                'showScriptName' => false,
                'rules' => [
                    '' => 'site/index',
                    'browserconfig.xml' => 'site/browserconfigxml',
                    'favicon.ico' => 'site/faviconico',
                    'robots.txt' => 'site/robotstxt',
                    'site.webmanifest' => 'site/webmanifest',
                    'extensions/<name>' => 'extensions/index',
                    'extensions/<name>/<section>' => 'extensions/view',
                    'music/lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>' => 'music/lyrics3tracks',
                    'music/lyrics/<artist:.*?>' => 'music/lyrics2albums',
                    'music/lyrics' => 'music/lyrics1artists',
                    'articles/<id:\d+>/<title:.*?>' => 'articles/article',
                    'articles/<id:\d+>' => 'articles/article',
                    'articles/<action:create|update|delete>/<id:.*>' => 'articles/<action>',
                    'articles/<action:new|delete|toggle>comment/<id:.*>' => 'articles/<action>comment',
                    'articles/search' => 'articles/search',
                    'articles/tag/<tag:\w+>' => 'articles/tag',
                    'articles/page-<page:\d+>' => 'articles/index',
                    '<controller:articles|calculator|extensions|tools>' => '<controller>/index',
                    'articles/<action>' => 'articles/<action>',
                    '<alias:\w+>' => 'site/<alias>',
                ],
            ],
            'mr42' => [
                'class' => 'yii\web\UrlManager',
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'baseUrl' => $this->params['shortDomain'],
                'rules' => ((new \mr42\Web())->getComponents())['urlManager']['rules'],
            ],
        ];
    }

    private function getDomainProperty(string $property): string
    {
        $host = ArrayHelper::getValue($_SERVER, 'SERVER_NAME');
        $domain = substr($host, 0, 4) === 'www.' ? substr($host, 4) : $host;
        switch ($domain) :
        case 'mister42.de':
            $properties = ['lang' => 'de', 'title' => 'Mr.42'];
        break;
        case 'xn--42-mlclt0afi.xn--p1ai':
            $properties = ['lang' => 'ru', 'title' => 'Г-н.42'];
        break;
        default:
            $properties = ['lang' => 'en', 'title' => 'Mr.42'];
        endswitch;

        return $properties[$property];
    }
}
