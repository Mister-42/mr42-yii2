<?php
namespace app\assets;
use Yii;
use app\models\Formatter;
use yii\web\{AssetBundle, View};

class AppAsset extends AssetBundle
{
	public $sourcePath = '@app/assets/src/css';

	public $css = [
		'site.scss',
	];

	public $js = [
	];

	public $depends = [
 		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
	];

	public function init()
	{
		Yii::$app->view->registerJs(Formatter::jspack('scrolltop.js'), View::POS_READY);
		Yii::$app->view->registerJs(Formatter::jspack('targetBlank.js'), View::POS_READY);
		Yii::$app->view->registerJs(Formatter::jspack('tooltip.js'), View::POS_READY);
	}

	public $publishOptions = [
		'forceCopy' => YII_ENV_DEV,
	];
}
