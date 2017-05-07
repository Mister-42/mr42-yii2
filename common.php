<?php
$secrets = require(__DIR__ . '/secrets.php');

return [
	'basePath' => __DIR__,
	'bootstrap' => ['log'],
	'components' => [
		'assetManager' => [
			'bundles' => [
				'yii\bootstrap\BootstrapAsset' => [
					'css' => [],
				],
				'yii\bootstrap\BootstrapPluginAsset' => [
					'js' => [YII_ENV_DEV ? 'js/bootstrap.js' : 'js/bootstrap.min.js'],
				],
				'yii\jui\JuiAsset' => [
					'css' => [YII_ENV_DEV ? 'themes/smoothness/jquery-ui.css' : 'themes/smoothness/jquery-ui.min.css'],
					'js' => [YII_ENV_DEV ? 'jquery-ui.js' : 'jquery-ui.min.js'],
				],
				'yii\web\JqueryAsset' => [
					'js' => [YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js'],
				],
			],
			'linkAssets' => true,
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
			'tablePrefix' => 'mr42_',

			'enableSchemaCache' => true,
			'schemaCache' => 'fileCache',
			'schemaCacheDuration' => 60*60*24*7,

			'enableQueryCache' => true,
			'queryCache' => 'fileCache',
			'queryCacheDuration' => 60*60*24*2,
		],
		'formatter' => [
			'class' => 'app\models\Formatter',
		],
		'i18n' => [
			'translations' => [
				'site' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'en',
				],
			],
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'except' => ['yii\web\HttpException:404'],
					'levels' => ['error'],
					'logFile' => '@runtime/logs/error.log',
				], [
					'class' => 'yii\log\FileTarget',
					'categories' => ['yii\web\HttpException:404'],
					'levels' => ['error', 'warning'],
					'logFile' => '@runtime/logs/404.log',
				],
			],
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
		],
		'pdf' => [
			'class' => \kartik\mpdf\Pdf::classname(),
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
				'BingSiteAuth.xml'													=> 'site/bing-site-auth',
				'favicon.ico'														=> 'site/faviconico',
				'robots.txt'														=> 'site/robotstxt',
				'sitemap.xml'														=> 'feed/sitemap',
				'lyrics/recenttracks'												=> 'lyrics/recenttracks',
				'lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>.pdf'					=> 'lyrics/albumpdf',
				'lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>-<size:.{2,5}>.jpg'	=> 'lyrics/albumcover',
				'lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>'						=> 'lyrics/index',
				'lyrics/<artist:.*?>'												=> 'lyrics/index',
				'articles/<id:\d+>/<title:.*?>.pdf'									=> 'articles/pdf',
				'articles/<id:\d+>/<title:.*?>'										=> 'articles/index',
				'articles/<id:\d+>'													=> 'articles/index',
				'articles/<action:search>'											=> 'articles/index',
				'articles/<action:tag>/<tag:\w+>'									=> 'articles/index',
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
	'params' => require(__DIR__ . '/params.php'),
	'runtimePath' => __DIR__ . '/../../.cache/yii/mr42',
	'timeZone' => 'Europe/Berlin',
	'vendorPath' => __DIR__ . '/../vendor',
];
