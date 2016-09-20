<?php
namespace app\controllers;
use Yii;
use app\models\tools\Favicon;
use app\models\tools\PhoneticAlphabet;
use yii\base\Object;
use yii\filters\HttpCache;
use yii\web\Controller;
use yii\web\UploadedFile;

class ToolsController extends Controller
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
				'only' => ['password'],
			],
		];
	}

	public function actionIndex()
	{
		return $this->goHome();
	}

	public function actionHeaders()
	{
		return $this->render('headers');
	}

	public function actionFavicon()
	{
		$model = new Favicon;

		if ($model->load(Yii::$app->request->post())) {
			$model->sourceImage = UploadedFile::getInstance($model, 'sourceImage');
			if ($model->convertImage()) {
				return $this->refresh();
			}
		}

		return $this->render('favicon', [
			'model' => $model,
		]);
	}

	public function actionPassword()
	{
		return $this->render('password');
	}

	public function actionPhoneticAlphabet()
	{
		$model = new PhoneticAlphabet;

		if ($model->load(Yii::$app->request->post())) {
			if ($model->convertText()) {
				return $this->refresh();
			}
		}

		return $this->render('phonetic-alphabet', [
			'model' => $model,
		]);
	}
}
