<?php
namespace app\controllers;
use Yii;
use app\models\calculator\{Date, Duration, Office365, Timezone};
use yii\base\BaseObject;
use yii\filters\HttpCache;

class CalculatorController extends \yii\web\Controller {
	public function behaviors() {
		return [
			[
				'class' => HttpCache::class,
				'enabled' => !YII_DEBUG,
				'etagSeed' => function(BaseObject $action) {
					return serialize([phpversion(), Yii::$app->user->id, file(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)]);
				},
				'lastModified' => function(BaseObject $action) {
					return filemtime(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'));
				},
				'only' => ['wpapsk'],
			],
		];
	}

	public function actionDate() {
		$model = new Date;
		if ($model->load(Yii::$app->request->post())) :
			$model->calculate();
		endif;

		return $this->render('date', [
			'model' => $model,
		]);
	}

	public function actionDuration() {
		$model = new Duration;
		if ($model->load(Yii::$app->request->post())) :
			$model->calculate();
		endif;

		return $this->render('duration', [
			'model' => $model,
		]);
	}

	public function actionOffice365() {
		$model = new Office365;
		if ($model->load(Yii::$app->request->post())) :
			$model->calculate();
		endif;

		return $this->render('office365', [
			'model' => $model,
		]);
	}

	public function actionTimezone() {
		$model = new Timezone;
		if ($model->load(Yii::$app->request->post())) :
			$model->calculate();
		endif;

		return $this->render('timezone', [
			'model' => $model,
		]);
	}

	public function actionWpapsk() {
		return $this->render('wpapsk');
	}
}
