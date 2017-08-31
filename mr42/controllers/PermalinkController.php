<?php
namespace app\controllers;
use app\models\Articles;
use yii\web\Controller;
use yii\helpers\Url;

class PermalinkController extends Controller {
	public function actionArticles($id) {
		$articles = new Articles;
		$article = Articles::find()->select(['id', 'title', 'url'])->where(['id' => $id])->one();

		if (count($article) === 0)
			die("Not found.");

		$this->redirect('https://www.mister42.me/' . Url::to(['articles/index', 'id' => $article->id, 'title' => $article->url]), 301)->send();
	}
}
