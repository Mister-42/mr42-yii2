<?php
namespace app\controllers;
use Yii;
use app\models\calculator\Date;
use app\models\calculator\Duration;
use app\models\calculator\Office365;
use yii\base\Object;
use yii\web\Controller;
use yii\filters\HttpCache;

class CalculatorController extends Controller
{
	public function behaviors()
	{
		return [
			[
				'class' => HttpCache::className(),
				'etagSeed' => function (Object $action, $params) {
					return serialize([YII_DEBUG, Yii::$app->user->id, file(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'))]);
				},
				'lastModified' => function (Object $action, $params) {
					return filemtime(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'));
				},
			],
		];
	}

	public function actionDate() {
		$model = new Date;

		if ($model->load(Yii::$app->request->post()))
			$model->diff();

		return $this->render('date', [
			'model' => $model,
		]);
	}

	public function actionDuration() {
		$model = new Duration;

		if ($model->load(Yii::$app->request->post()))
			$model->duration();

		return $this->render('duration', [
			'model' => $model,
		]);
	}

	public function actionOffice365() {
		$model = new Office365;

		if ($model->load(Yii::$app->request->post()))
			$model->calcEndDate();

		return $this->render('office365', [
			'model' => $model,
		]);
	}

	public function beforeAction($action) {
		if (in_array($action->id, ['office365'])) {
			$this->enableCsrfValidation = false;
		}
		return parent::beforeAction($action);
	}
}
