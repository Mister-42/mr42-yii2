<?php
namespace app\controllers;
use Yii;
use app\models\{Changelog, MenuItems};
use app\models\site\Contact;
use yii\bootstrap\Alert;
use yii\base\Object;
use yii\captcha\CaptchaAction;
use yii\filters\{AccessControl, HttpCache};
use yii\web\{Controller, ErrorAction, NotFoundHttpException, Response};

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
				'only' => ['playground'],
				'rules' => [
					[
						'actions' => ['playground'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function ($rule, $action) {
					throw new NotFoundHttpException('Page not found.');
				}
			], [
				'class' => HttpCache::className(),
				'only' => ['changelog'],
				'lastModified' => function (Object $action, $params) {
					$lastUpdate = Changelog::find()->select(['time' => 'max(time)'])->one();
					return $lastUpdate->time;
				},
			], [
				'class' => HttpCache::className(),
				'only' => ['credits', 'robotstxt'],
				'lastModified' => function (Object $action, $params) {
					return filemtime(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'));
				},
			], [
				'class' => HttpCache::className(),
				'only' => ['faviconico'],
				'lastModified' => function (Object $action, $params) {
					return filemtime(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/favicon.ico');
				},
			],
		];
	}

	public function actionIndex() {
		$this->layout = '@app/views/layouts/column2.php';

		return  $this->render('index', [
			'model' => $model,
			'pages' => MenuItems::menuArray(),
		]);
	}

	public function actionChangelog() {
		return $this->render('changelog');
	}

	public function actionContact() {
		$model = new Contact;
		if ($model->load(Yii::$app->request->post()) && $model->contact())
			return Alert::widget(['options' => ['class' => 'alert-success'], 'body' => 'Thank you for contacting us. We will respond to you as soon as possible.', 'closeButton' => false]);

		return $this->render('contact', [
			'model' => $model,
		]);
	}

	public function actionCredits() {
		return $this->render('credits');
	}

	public function actionFaviconico() {
		Yii::$app->response->sendFile(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/favicon.ico', 'favicon.ico', ['inline' => true]);
	}

	public function actionOffline() {
		Yii::$app->response->statusCode = 503;
		Yii::$app->response->headers->add('Retry-After', 900);
		return $this->render('offline');
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
