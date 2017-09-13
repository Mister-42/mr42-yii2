<?php
namespace app\controllers;
use Yii;
use app\models\articles\Articles;
use app\models\tools\{Country, Favicon, PhoneticAlphabet, Qr};
use yii\base\{BaseObject, ViewNotFoundException};
use yii\filters\HttpCache;
use yii\helpers\{ArrayHelper, FileHelper};
use yii\web\{NotFoundHttpException, UploadedFile};

class ToolsController extends \yii\web\Controller {
	public function behaviors() {
		return [
			[
				'class' => HttpCache::className(),
				'etagSeed' => function (BaseObject $action) {
					return serialize([YII_DEBUG, phpversion(), Yii::$app->user->id, file(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'))]);
				},
				'lastModified' => function (BaseObject $action) {
					return filemtime(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'));
				},
				'only' => ['html-to-markdown', 'password'],
			],
		];
	}

	public function actionCountry() {
		return $this->render('country', [
			'model' => new Country,
		]);
	}

	public function actionFavicon() {
		if (!file_exists(Yii::getAlias('@webroot/assets/temp/favicon')))
			FileHelper::createDirectory(Yii::getAlias('@webroot/assets/temp/favicon'));

		$model = new Favicon;
		if ($model->load(Yii::$app->request->post())) {
			$model->sourceImage = UploadedFile::getInstance($model, 'sourceImage');
			if ($model->convertImage())
				return $this->refresh();
		}

		return $this->render('favicon', [
			'model' => $model,
		]);
	}

	public function actionHeaders() {
		return $this->render('headers');
	}

	public function actionHtmlToMarkdown() {
		return $this->render('html-to-markdown', [
			'lastPost' => Articles::find()->orderBy('id DESC')->one(),
		]);
	}

	public function actionPassword() {
		return $this->render('password');
	}

	public function actionPhoneticAlphabet() {
		$model = new PhoneticAlphabet;
		if ($model->load(Yii::$app->request->post()) && $model->validate())
			$model->convertText();

		return $this->render('phonetic-alphabet', [
			'model' => $model,
		]);
	}

	public function actionQr() {
		if (!file_exists(Yii::getAlias('@webroot/assets/temp/qr')))
			FileHelper::createDirectory(Yii::getAlias('@webroot/assets/temp/qr'));

		$model = new Qr;
		if (Yii::$app->request->isPost) {
			$type = ArrayHelper::getValue(Yii::$app->request->post(), 'type')
				?? ArrayHelper::getValue(Yii::$app->request->post(), 'qr.type');

			$modelName = '\\app\\models\\tools\\qr\\' . $type;
			$model = new $modelName;
			$model->type = $type;

			if (Yii::$app->request->isAjax)
				try {
					return $this->renderAjax('qr/' . strtolower($type == 'Sms' ? 'Phone' : $type), [
						'model' => $model,
					]);
				} catch (ViewNotFoundException $e) {
					throw new NotFoundHttpException('Type ' . $type . ' not found.');
				}

			$model = ArrayHelper::merge($model, ArrayHelper::getValue(Yii::$app->request->post(), 'qr'));
			if ($model->validate())
				$model->generateQr();
			$qrForm = $this->renderPartial('qr/' . strtolower($type == 'Sms' ? 'Phone' : $type), [
				'model' => $model
			]);
		}

		return $this->render('qr', [
			'model' => $model,
			'qrForm' => $qrForm
		]);
	}
}
