<?php
namespace app\controllers;
use Yii;
use app\models\calculator\{Date, Duration, Office365, Timezone};
use yii\base\Object;
use yii\filters\HttpCache;

class CalculatorController extends \yii\web\Controller {
	public function behaviors() {
		return [
			[
				'class' => HttpCache::className(),
				'etagSeed' => function (Object $action, $params) {
					return serialize([YII_DEBUG, Yii::$app->user->id, file(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'))]);
				},
				'lastModified' => function (Object $action, $params) {
					return filemtime(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'));
				},
				'only' => ['wpapsk'],
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

	public function actionTimezone() {
		$model = new Timezone;
		if ($model->load(Yii::$app->request->post()))
			$model->diff();

		return $this->render('timezone', [
			'model' => $model,
		]);
	}

	public function actionWpapsk() {
		return $this->render('wpapsk');
	}
}
