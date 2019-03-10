<?php
namespace app\controllers;
use Yii;
use app\models\site\{Contact, Webmanifest};
use yii\base\BaseObject;
use yii\filters\{AccessControl, HttpCache};
use yii\web\{ErrorAction, NotFoundHttpException, Response, UploadedFile};

class SiteController extends \yii\web\Controller {
	public function actions(): array {
		return [
			'error' => [
				'class' => ErrorAction::class,
			],
		];
	}

	public function behaviors(): array {
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
				'etagSeed' => function(BaseObject $action) {
					$file = ($action->id === 'faviconico') ? '@assetsroot/images/favicon.ico' : "@app/views/{$action->controller->id}/{$action->id}.php";
					return serialize([phpversion(), Yii::$app->user->id, Yii::$app->view->renderFile($file)]);
				},
				'lastModified' => function(BaseObject $action) {
					return filemtime(Yii::getAlias(($action->id === 'faviconico') ? '@assetsroot/images/favicon.ico' : "@app/views/{$action->controller->id}/{$action->id}.php"));
				},
				'except' => ['index', 'contact', 'offline', 'webmanifest'],
			],
		];
	}

	public function actionIndex(): string {
		$this->layout = 'columns';
		return $this->render('index');
	}

	public function actionContact(): string {
		$model = new Contact();
		if ($model->load(Yii::$app->request->post())) :
			$model->attachment = UploadedFile::getInstance($model, 'attachment');
			if ($model->contact())
				return $this->renderAjax('contact-success');
		endif;

		return $this->render('contact', [
			'model' => $model,
		]);
	}

	public function actionFaviconico(): Response {
		return Yii::$app->response->sendFile(Yii::getAlias('@assetsroot/images/favicon.ico'), 'favicon.ico', ['inline' => true]);
	}

	public function actionOffline(): string {
		Yii::$app->response->statusCode = 503;
		Yii::$app->response->headers->add('Retry-After', 900);
		return $this->render('offline');
	}

	public function actionPhp(): string {
		return $this->render('php');
	}

	public function actionBrowserconfigxml(): string {
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'application/xml');
		return $this->renderPartial('browserconfigxml');
	}

	public function actionPrivacy(): string {
		return $this->render('privacy');
	}

	public function actionRobotstxt(): string {
		Yii::$app->response->format = Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'text/plain');
		return $this->renderPartial('robotstxt');
	}

	public function actionWebmanifest(): Response {
		return $this->asJson(Webmanifest::getData());
	}
}
