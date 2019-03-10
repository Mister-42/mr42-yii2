<?php
namespace app\controllers;
use Yii;
use yii\base\BaseObject;
use yii\filters\HttpCache;

class MyController extends \yii\web\Controller {
	public function behaviors(): array {
		return [
			[
				'class' => HttpCache::class,
				'enabled' => !YII_DEBUG,
				'etagSeed' => function(BaseObject $action) {
					$file = "@app/views/{$action->controller->id}/{$action->id}.php";
					return serialize([phpversion(), Yii::$app->user->id, Yii::$app->view->renderFile($file)]);
				},
				'lastModified' => function(BaseObject $action) {
					return filemtime(Yii::getAlias("@app/views/{$action->controller->id}/{$action->id}.php"));
				},
			],
		];
	}

	public function actionPi(): string {
		return $this->render('pi');
	}
}
