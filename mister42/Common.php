<?php

namespace mister42;

class Common
{
    private $params;
    private $secrets;

    public function __construct()
    {
        $this->params = (new Params())->getValues();
        $this->secrets = (new Secrets())->getValues();
    }

    public function getValues(): array
    {
        return [
            'aliases' => [
                '@assets' => '//s.mr42.me',
                '@assetsroot' => __DIR__ . '/../../webassets/me.mr42.s',
                '@bower' => '@vendor/bower-asset',
                '@npm' => '@vendor/npm-asset',
            ],
            'bootstrap' => ['log'],
            'components' => $this->getComponents(),
            'language' => 'en',
            'name' => 'Mr.42',
            'params' => $this->params,
            'runtimePath' => __DIR__ . '/../../../.cache/yii/mister42',
            'timeZone' => 'Europe/Berlin',
            'vendorPath' => __DIR__ . '/../../vendor',
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
                'attributes' => [
                    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
                ],
                'enableSchemaCache' => true,
                'schemaCache' => 'fileCache',
                'schemaCacheDuration' => 60 * 60 * 24 * 7,
                'enableQueryCache' => true,
                'queryCache' => 'fileCache',
                'queryCacheDuration' => 60 * 60 * 24 * 2,
            ],
            'formatter' => [
                'class' => 'app\models\Formatter',
            ],
            'icon' => [
                'class' => 'app\models\Icon',
                'config' => [
                    'prefix' => 'icon',
                    'registerAssets' => false,
                ],
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
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => $this->secrets['email']['host'],
                    'username' => $this->secrets['email']['username'],
                    'password' => $this->secrets['email']['password'],
                    'encryption' => 'tls',
                ],
            ],
            'urlManager' => [
                'class' => 'codemix\localeurls\UrlManager',
                'enablePrettyUrl' => true,
                'ignoreLanguageUrlPatterns' => [
                    '#^feed/(rss|sitemap)#' => '#feed/(rss|sitemap)#',
                    '#^site/(browserconfigxml|faviconico|robotstxt|webmanifest)#' => '#site/(browserconfigxml|faviconico|robotstxt|webmanifest)#',
                    '#^music/(albumpdf|albumcover|collection-cover)#' => '#music/(albumpdf|albumcover|collection-cover)#',
                    '#^articles/pdf#' => '#articles/pdf#',
                ],
                'languages' => array_keys($this->params['languages']),
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
                    'sitemap.xml' => 'feed/sitemap',
                    'sitemap-articles.xml' => 'feed/sitemap-articles',
                    'sitemap-lyrics.xml' => 'feed/sitemap-lyrics',
                    'music/lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>.pdf' => 'music/albumpdf',
                    'music/lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>-<size:.{2,5}>.jpg' => 'music/albumcover',
                    'music/lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>' => 'music/lyrics',
                    'music/lyrics/<artist:.*?>' => 'music/lyrics',
                    'music/collection-cover/<id:.*>.jpg' => 'music/collection-cover',
                    'articles/<id:\d+>/<title:.*?>.pdf' => 'articles/pdf',
                    'articles/<id:\d+>/<title:.*?>' => 'articles/article',
                    'articles/<id:\d+>' => 'articles/article',
                    'articles/<action:create|update|delete>/<id:.*>' => 'articles/<action>',
                    'articles/<action:new|delete|toggle>comment/<id:.*>' => 'articles/<action>comment',
                    'articles/search' => 'articles/search',
                    'articles/tag/<tag:\w+>' => 'articles/tag',
                    'articles/page-<page:\d+>' => 'articles/index',
                    '<controller:articles|calculator|feed|test|tools>' => '<controller>/index',
                    'articles/<action>' => 'articles/<action>',
                    '<alias:\w+>' => 'site/<alias>',
                ],
            ],
            'urlManagerMr42' => [
                'class' => 'yii\web\UrlManager',
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'baseUrl' => $this->params['shortDomain'],
                'rules' => ((new \mr42\Web())->getComponents())['urlManager']['rules'],
            ],
        ];
    }
}
