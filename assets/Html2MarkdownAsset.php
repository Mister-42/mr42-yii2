<?php
namespace app\assets;
use Yii;
use yii\web\{AssetBundle, View};

class Html2MarkdownAsset extends AssetBundle {
	public $sourcePath = '@bower/to-markdown/dist';

	public $js = [
		'to-markdown.js',
	];

	public $depends = [
 		'app\assets\AppAsset',
	];

	public function registerAssetFiles($view) {
		parent::registerAssetFiles($view);
		Yii::$app->view->registerJs(Yii::$app->formatter->jspack('tools/html-to-markdown.js'), View::POS_HEAD);
	}
}
