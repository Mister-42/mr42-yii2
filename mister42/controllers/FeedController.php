<?php
namespace app\controllers;
use Yii;
use app\models\articles\Articles;
use yii\filters\HttpCache;
use yii\helpers\{ArrayHelper, StringHelper};
use yii\web\Response;

class FeedController extends \yii\web\Controller {
	public function behaviors() {
		$lastUpdate = Articles::find()->select(['updated' => 'max(updated)'])->one();
		return [
			[
				'class' => HttpCache::class,
				'enabled' => !YII_DEBUG,
				'except' => ['index'],
				'etagSeed' => function() { return serialize([phpversion(), Yii::$app->user->id, $lastUpdate->updated]); },
				'lastModified' => function() { return $lastUpdate->updated; },
				'only' => ['index', 'rss'],
			],
		];
	}

	public function actionIndex() {
		$this->redirect(['rss'], 301)->send();
	}

	public function actionRss() {
		if (!StringHelper::startsWith(Yii::$app->request->headers->get('user-agent'), 'FeedBurner') && !ArrayHelper::isIn(Yii::$app->request->userIP, Yii::$app->params['secrets']['params']['specialIPs'])) :
			$this->redirect('http://f.mr42.me/Mr42')->send();
		endif;

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
