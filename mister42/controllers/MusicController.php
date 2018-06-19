<?php
namespace app\controllers;
use Yii;
use app\models\music\Collection;

class MusicController extends \yii\web\Controller {
	public function actionCollection() {
		return $this->render('collection', [
			'model' => new Collection(),
		]);
	}

	public function actionCollectionCover(int $id) {
		$album = Collection::find()->where(['id' => $id])->one();
		return Yii::$app->response->sendContentAsFile($album->image, "{$id}.jpg", ['mimeType' => 'image/jpeg', 'inline' => true]);
	}
}
