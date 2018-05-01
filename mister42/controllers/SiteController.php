<?php
namespace app\controllers;
use Yii;
use app\models\site\{Changelog, Contact, Webmanifest};
use yii\bootstrap4\Html;
use yii\base\BaseObject;
use yii\captcha\CaptchaAction;
use yii\filters\{AccessControl, HttpCache};
use yii\web\{ErrorAction, NotFoundHttpException, Response, UploadedFile};

class SiteController extends \yii\web\Controller {
	public function actions() {
		return [
			'captcha' => [
				'class' => CaptchaAction::class,
				'backColor' => 0xffffff,
				'foreColor' => 0x003e67,
				'transparent' => true,
			],
			'error' => [
				'class' => ErrorAction::class,
			],
		];
	}

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::class,
				'only' => ['php-version'],
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function () {
					throw new NotFoundHttpException('Page not found.');
				}
			], [
				'class' => HttpCache::class,
				'enabled' => !YII_DEBUG,
				'etagSeed' => function (BaseObject $action) {
					return serialize([phpversion(), Yii::$app->user->id, file(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)]);
				},
				'lastModified' => function () {
					$lastUpdate = Changelog::find()->select(['time' => 'max(time)'])->one();
					return $lastUpdate->time;
				},
				'only' => ['changelog'],
			], [
				'class' => HttpCache::class,
				'enabled' => !YII_DEBUG,
				'etagSeed' => function () {
					return serialize(file(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/favicon.ico'));
				},
				'lastModified' => function () {
					return filemtime(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/favicon.ico');
				},
				'only' => ['faviconico'],
			], [
				'class' => HttpCache::class,
				'enabled' => !YII_DEBUG,
				'etagSeed' => function (BaseObject $action) {
					return serialize([phpversion(), file(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)]);
				},
				'lastModified' => function (BaseObject $action) {
					return filemtime(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'));
				},
				'only' => ['browserconfigxml', 'robotstxt'],
			],
		];
	}

	public function actionIndex() {
		$this->layout = 'columns';
		return $this->render('index');
	}

	public function actionChangelog() {
		return $this->render('changelog');
	}

	public function actionContact() {
		$model = new Contact();
		if ($model->load(Yii::$app->request->post())) {
			$model->attachment = UploadedFile::getInstance($model, 'attachment');
			if ($model->contact())
				return Html::tag('div', 'Thank you for contacting us. We will respond to you as soon as possible.', ['class' => 'alert alert-success']);
		}

		return $this->render('contact', [
			'model' => $model,
		]);
	}

	public function actionFaviconico() {
		Yii::$app->response->sendFile(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/favicon.ico', 'favicon.ico', ['inline' => true]);
	}

	public function actionOffline() {
		Yii::$app->response->statusCode = 503;
		Yii::$app->response->headers->add('Retry-After', 900);
		return $this->render('offline');
	}

	public function actionPhpVersion() {
		return $this->render('php-version');
	}

	public function actionBrowserconfigxml() {
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'application/xml');
		return $this->renderPartial('browserconfigxml');
	}

	public function actionRobotstxt() {
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'text/plain');
		return $this->renderPartial('robotstxt');
	}

	public function actionWebmanifest() {
		return $this->asJson(Webmanifest::getData());
	}
}
