<?php
namespace app\assets;
use Yii;
use yii\web\{AssetBundle, View};

class AppAsset extends AssetBundle {
	public $sourcePath = '@runtime/assets/css';

	public $css = [
		'site.css',
	];

	public $js = [
	];

	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap4\BootstrapAsset',
		'app\assets\FontAwesomeAsset',
	];

	public function init() {
		Yii::$app->view->registerJs(Yii::$app->formatter->jspack('scrolltop.js'), View::POS_READY);
		Yii::$app->view->registerJs(Yii::$app->formatter->jspack('targetBlank.js'), View::POS_READY);
		Yii::$app->view->registerJs(Yii::$app->formatter->jspack('tooltip.js'), View::POS_READY);
	}
}
