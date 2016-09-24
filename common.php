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
			'converter' => [
				'class' => 'yii\web\AssetConverter',
				'commands' => [
					'scss' => ['css', 'sass {from} {to} -C --sourcemap=none -t compressed -I '.realpath(__DIR__.'/../vendor/bower/bootstrap-sass/assets/stylesheets')],
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
					'class' => 'yii\log\DbTarget',
					'levels' => ['error'],
					'logTable' => 'x_log',
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
				'favicon.ico'													=> 'site/faviconico',
				'robots.txt'													=> 'site/robotstxt',
				'sitemap.xml'													=> 'site/sitemapxml',
				'feed/rss'														=> 'site/rss',
				'lyrics/recenttracks'										=> 'lyrics/recenttracks',
				'lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>.pdf'	=> 'lyrics/albumpdf',
				'lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>'		=> 'lyrics/index',
				'lyrics/<artist:.*?>'										=> 'lyrics/index',
				'lyrics'															=> 'lyrics/index',
				'article/<id:\d+>/<title:.*?>.pdf'						=> 'post/pdf',
				'article/<id:\d+>/<title:.*?>'							=> 'post/index',
				'articles/<action:tag>/<tag:\w+>'						=> 'post/index',
				'articles/<action:search>'									=> 'post/index',
				'articles/page-<page:\d+>'									=> 'post/index',
				'article/<id:\d+>'											=> 'post/index',
				'articles'														=> 'post/index',
				'article/<action>'											=> 'post/<action>',
				'<alias:\w+>'													=> 'site/<alias>',
			],
		],
	],
	'language' => 'en',
	'name' => 'Mr.42',
	'params' => require(__DIR__ . '/params.php'),
	'runtimePath' => __DIR__ . '/../yii-runtime/me.mr42.www',
	'timeZone' => 'Europe/Berlin',
	'vendorPath' => __DIR__ . '/../vendor',
];
