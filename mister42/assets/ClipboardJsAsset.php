<?php
namespace app\assets;
use Yii;
use yii\web\{AssetBundle, View};

class ClipboardJsAsset extends AssetBundle {
	public $sourcePath = '@bower/clipboard/dist';

	public $js = [
		'clipboard.min.js',
	];

	public $depends = [
 		'app\assets\AppAsset',
	];

	public function registerAssetFiles($view) {
		parent::registerAssetFiles($view);
		Yii::$app->view->registerJs(Yii::$app->formatter->jspack('clipboard.js'), View::POS_READY);
	}
}
