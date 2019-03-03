<?php
namespace app\controllers;
use Yii;
use app\models\articles\Articles;
use yii\filters\HttpCache;
use yii\helpers\{ArrayHelper, StringHelper};
use yii\web\Response;

class FeedController extends \yii\web\Controller {
	public function behaviors(): array {
		$lastModified = Articles::getLastModified();
		return [
			[
				'class' => HttpCache::class,
				'enabled' => !YII_DEBUG,
				'except' => ['index'],
				'etagSeed' => function() { return serialize([phpversion(), Yii::$app->user->id, $lastModified]); },
				'lastModified' => function() { return $lastModified; },
				'only' => ['rss', 'sitemap-articles'],
			],
		];
	}

	public function actionIndex(): Response {
		return $this->redirect(['rss'], 301);
	}

	public function actionRss(): string {
		if (php_sapi_name() !== 'cli' && !StringHelper::startsWith(Yii::$app->request->headers->get('user-agent'), 'FeedBurner') && !ArrayHelper::isIn(Yii::$app->request->userIP, Yii::$app->params['secrets']['params']['specialIPs']))
			$this->redirect('http://f.mr42.me/Mr42')->send();

		$articles = Articles::find()
			->orderBy(['updated' => SORT_DESC])
			->with('author')
			->limit(5)
			->all();

		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'application/rss+xml');
		return $this->renderPartial('rss', [
			'articles' => $articles,
		]);
	}

	public function actionSitemap(): string {
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'application/xml');
		return $this->renderPartial(Yii::$app->controller->action->id);
	}

	public function actionSitemapArticles(): string {
		return $this->actionSitemap();
	}

	public function actionSitemapLyrics(): string {
		return $this->actionSitemap();
	}
}
