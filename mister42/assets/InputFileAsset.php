<?php
namespace app\assets;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\Json;
use yii\web\{AssetBundle, View};

class InputFileAsset extends AssetBundle {
	public static function register($view) {
		$options = Json::encode([
			'lang' => [
				'selected' => Yii::t('mr42', 'File \'{name}\' Selected', ['name' => Html::tag('span', null, ['class' => 'filename'])])
			]
		]);

		$view->registerJs("var inputFile = {$options};", View::POS_READY);
		Yii::$app->view->registerJs(Yii::$app->formatter->jspack('inputFile.js'), View::POS_READY);

		return parent::register($view);
	}
}
