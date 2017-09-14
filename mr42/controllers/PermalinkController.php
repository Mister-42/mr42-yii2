<?php
namespace app\controllers;
use Yii;
use app\models\Articles;
use yii\helpers\Url;

class PermalinkController extends \yii\web\Controller {
	public function actionArticles($id) {
		$article = Articles::find()->select(['id', 'title', 'url'])->where(['id' => $id]);
		if ($article->count() === 0) {
			Yii::$app->response->statusCode = 404;
			return 'Not found.';
		}

		$article = $article->one();
		$this->redirect('https://www.mister42.me/' . Url::to(['articles/index', 'id' => $article->id, 'title' => $article->url]), 301)->send();
	}
}
