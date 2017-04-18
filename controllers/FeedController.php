<?php
namespace app\controllers;
use Yii;
use app\models\articles\Articles;
use yii\base\Object;
use yii\filters\HttpCache;
use yii\helpers\{ArrayHelper, StringHelper};
use yii\web\{Controller, Response};

class FeedController extends Controller {
	public function behaviors() {
		return [
			[
				'class' => HttpCache::className(),
				'except' => ['index'],
				'lastModified' => function (Object $action, $params) {
					$lastUpdate = Articles::find()->select(['updated' => 'max(updated)'])->one();
					return $lastUpdate->updated;
				},
			],
		];
	}

	public function actionIndex() {
		$this->redirect(['rss'], 301)->send();
	}

	public function actionRss() {
		if (!Stringhelper::startsWith($_SERVER[HTTP_USER_AGENT], 'FeedBurner') && !ArrayHelper::isIn(Yii::$app->getRequest()->getUserIP(), Yii::$app->params['specialIPs']))
			$this->redirect('http://feed.mr42.me/Mr42')->send();

		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'application/rss+xml');

		$articles = Articles::find()
			->orderBy('updated DESC')
			->with('user')
			->limit(5)
			->all();

		return $this->renderPartial('rss', [
			'articles' => $articles,
		]);
	}

	public function actionSitemap() {
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'application/xml');

		return $this->renderPartial('sitemap');
	}
}
