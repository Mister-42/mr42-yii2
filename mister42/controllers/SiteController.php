<?php
namespace app\controllers;
use Yii;
use app\models\site\{Contact, Webmanifest};
use yii\base\BaseObject;
use yii\filters\{AccessControl, HttpCache};
use yii\web\{ErrorAction, NotFoundHttpException, Response, UploadedFile};

class SiteController extends \yii\web\Controller {
	public function actions() {
		return [
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
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
				'only' => ['php'],
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function() {
					throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
				}
			], [
				'class' => HttpCache::class,
				'enabled' => !YII_DEBUG,
				'etagSeed' => function() {
					return serialize(file(Yii::getAlias('@assetsroot/images/favicon.ico')));
				},
				'lastModified' => function() {
					return filemtime(Yii::getAlias('@assetsroot/images/favicon.ico'));
				},
				'only' => ['faviconico'],
			], [
				'class' => HttpCache::class,
				'enabled' => !YII_DEBUG,
				'etagSeed' => function(BaseObject $action) {
					return serialize([phpversion(), file(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)]);
				},
				'lastModified' => function(BaseObject $action) {
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

	public function actionContact() {
		$model = new Contact();
		if ($model->load(Yii::$app->request->post())) :
			$model->attachment = UploadedFile::getInstance($model, 'attachment');
			if ($model->contact()) :
				return $this->renderAjax('contact-success');
			endif;
		endif;

		return $this->render('contact', [
			'model' => $model,
		]);
	}

	public function actionFaviconico() {
		Yii::$app->response->sendFile(Yii::getAlias('@assetsroot/images/favicon.ico'), 'favicon.ico', ['inline' => true]);
	}

	public function actionOffline() {
		Yii::$app->response->statusCode = 503;
		Yii::$app->response->headers->add('Retry-After', 900);
		return $this->render('offline');
	}

	public function actionPhp() {
		return $this->render('php');
	}

	public function actionBrowserconfigxml() {
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'application/xml');
		return $this->renderPartial('browserconfigxml');
	}

	public function actionPi() {
		return $this->render('pi');
	}

	public function actionPrivacy() {
		return $this->render('privacy');
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
