<?php
namespace app\controllers;
use Yii;
use app\models\site\{Changelog, Contact};
use yii\bootstrap\Alert;
use yii\base\Object as BaseObject;
use yii\captcha\CaptchaAction;
use yii\filters\{AccessControl, HttpCache};
use yii\web\{Controller, ErrorAction, NotFoundHttpException, Response, UploadedFile};

class SiteController extends Controller {
	public function actions() {
		return [
			'captcha' => [
				'class' => CaptchaAction::className(),
				'backColor' => 0xffffff,
				'foreColor' => 0x003e67,
				'transparent' => true,
			],
			'error' => [
				'class' => ErrorAction::className(),
			],
		];
	}

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['php-version', 'playground'],
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
				'class' => HttpCache::className(),
				'lastModified' => function () {
					$lastUpdate = Changelog::find()->select(['time' => 'max(time)'])->one();
					return $lastUpdate->time;
				},
				'only' => ['changelog'],
			], [
				'class' => HttpCache::className(),
				'lastModified' => function (BaseObject $action) {
					return filemtime(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'));
				},
				'only' => ['robotstxt'],
			], [
				'class' => HttpCache::className(),
				'lastModified' => function () {
					return filemtime(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/favicon.ico');
				},
				'only' => ['faviconico'],
			],
		];
	}

	public function actionIndex() {
		$this->layout = '@app/views/layouts/column2.php';
		return $this->render('index');
	}

	public function actionBingSiteAuth() {
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'application/xml');
		return $this->renderPartial('bing-site-auth');
	}

	public function actionChangelog() {
		return $this->render('changelog');
	}

	public function actionContact() {
		$model = new Contact;
		if ($model->load(Yii::$app->request->post())) {
			$model->attachment = UploadedFile::getInstance($model, 'attachment');
			if ($model->contact())
				return Alert::widget(['options' => ['class' => 'alert-success'], 'body' => 'Thank you for contacting us. We will respond to you as soon as possible.', 'closeButton' => false]);
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

	public function actionPlayground() {
		return $this->render('playground');
	}

	public function actionRobotstxt() {
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'text/plain');
		return $this->renderPartial('robotstxt');
	}
}
