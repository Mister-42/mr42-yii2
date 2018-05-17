<?php
$secrets = require(__DIR__.'/secrets.php');

return [
	'aliases' => [
		'@assets' => '//s.mister42.me',
		'@assetsroot' => __DIR__.'/../../www/assets/me.mister42.s',
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
	],
	'basePath' => __DIR__,
	'bootstrap' => ['log'],
	'components' => [
		'assetManager' => [
			'basePath' => '@assetsroot',
			'baseUrl' => '@assets',
			'bundles' => [
				'yii\bootstrap4\BootstrapAsset' => [
					'css' => [],
				],
				'yii\bootstrap4\BootstrapPluginAsset' => [
					'js' => [YII_DEBUG ? 'js/bootstrap.js' : 'js/bootstrap.min.js'],
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
			'dsn' => 'mysql:host='.$secrets['MySQL']['host'].';dbname='.$secrets['MySQL']['db'],
			'username' => $secrets['MySQL']['user'],
			'password' => $secrets['MySQL']['pass'],
			'charset' => 'utf8',
			'tablePrefix' => 'mister42_',

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
		],
		'i18n' => [
			'translations' => [
				'site' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
				],
			],
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => $secrets['email']['host'],
				'username' => $secrets['email']['username'],
				'password' => $secrets['email']['password'],
				'encryption' => 'tls',
			],
		],
		'pdf' => [
			'class' => \kartik\mpdf\Pdf::class,
			'mode' => \kartik\mpdf\Pdf::MODE_UTF8,
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'normalizer' => [
				'class' => 'yii\web\UrlNormalizer',
			],
			'showScriptName' => false,
			'rules' => [
				''																	=> 'site/index',
				'browserconfig.xml'													=> 'site/browserconfigxml',
				'favicon.ico'														=> 'site/faviconico',
				'robots.txt'														=> 'site/robotstxt',
				'site.webmanifest'													=> 'site/webmanifest',
				'sitemap.xml'														=> 'feed/sitemap',
				'lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>.pdf'					=> 'lyrics/albumpdf',
				'lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>-<size:.{2,5}>.jpg'	=> 'lyrics/albumcover',
				'lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>'						=> 'lyrics/index',
				'lyrics/<artist:.*?>'												=> 'lyrics/index',
				'articles/<id:\d+>/<title:.*?>.pdf'									=> 'articles/pdf',
				'art<id:\d+>'														=> 'permalink/articles',
				'articles/<id:\d+>/<title:.*?>'										=> 'articles/index',
				'articles/<id:\d+>'													=> 'articles/index',
				'articles/<action:search>'											=> 'articles/index',
				'articles/<action:tag>/<q:\w+>'										=> 'articles/index',
				'articles/page-<page:\d+>'											=> 'articles/index',
				'articles'															=> 'articles/index',
				'articles/<action>'													=> 'articles/<action>',
				'<controller:calculator|feed|lyrics|tools>'							=> '<controller>/index',
				'<alias:\w+>'														=> 'site/<alias>',
			],
		],
	],
	'language' => 'en',
	'name' => 'Mr.42',
	'params' => require(__DIR__.'/params.php'),
	'runtimePath' => __DIR__.'/../../../.cache/yii/mister42',
	'timeZone' => 'Europe/Berlin',
	'vendorPath' => __DIR__.'/../../vendor',
];
