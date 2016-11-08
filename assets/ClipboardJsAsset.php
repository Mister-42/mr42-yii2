<?php
namespace app\assets;
use Yii;
use app\models\Formatter;
use yii\web\{AssetBundle, View};

class ClipboardJsAsset extends AssetBundle {
	public $sourcePath = '@bower/clipboard/dist';

	public $js = [
		'clipboard.min.js',
	];

	public function registerAssetFiles($view) {
		parent::registerAssetFiles($view);
		Yii::$app->view->registerJs(Formatter::jspack('clipboard.js'), View::POS_READY);
	}

	public $depends = [
 		'app\assets\AppAsset',
	];

}
