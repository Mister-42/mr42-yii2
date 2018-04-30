<?php
namespace app\controllers;
use Yii;
use app\models\tools\{Barcode, Favicon, PhoneticAlphabet, Qr};
use yii\base\{BaseObject, ViewNotFoundException};
use yii\helpers\{ArrayHelper, FileHelper};
use yii\web\{NotFoundHttpException, UploadedFile};

class ToolsController extends \yii\web\Controller {
	public function behaviors() {
		return [
			[
				'class' => \yii\filters\HttpCache::class,
				'enabled' => !YII_DEBUG,
				'etagSeed' => function (BaseObject $action) {
					return serialize([phpversion(), Yii::$app->user->id, file(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'))]);
				},
				'lastModified' => function (BaseObject $action) {
					return filemtime(Yii::getAlias('@app/views/'.$action->controller->id.'/'.$action->id.'.php'));
				},
				'only' => ['html-to-markdown', 'password'],
			],
		];
	}

	public function actionBarcode() {
		if (!file_exists(Yii::getAlias('@assetsroot/temp')))
			FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));

		$model = new Barcode;
		if ($model->load(Yii::$app->request->post()) && $model->validate())
			$model->generate();

		return $this->render('barcode', [
			'model' => $model,
		]);
	}

	public function actionCountry() {
		return $this->render('country');
	}

	public function actionFavicon() {
		if (!file_exists(Yii::getAlias('@assetsroot/temp')))
			FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));

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
		return $this->render('html-to-markdown');
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
		if (!file_exists(Yii::getAlias('@assetsroot/temp')))
			FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));

		$model = new Qr;
		if (Yii::$app->request->isPost) {
			$type = ArrayHelper::getValue(Yii::$app->request->post(), 'type')
				?? ArrayHelper::getValue(Yii::$app->request->post(), 'qr.type');

			$modelName = '\\app\\models\\tools\\qr\\' . $type;
			$model = new $modelName;
			$model->type = $type === 'SMS' ? 'Phone' : $type;

			if (Yii::$app->request->isAjax)
				try {
					return $this->renderAjax('qr/' . strtolower($model->type), [
						'model' => $model,
					]);
				} catch (ViewNotFoundException $e) {
					throw new NotFoundHttpException('Type ' . $type . ' not found.');
				}

			$model = ArrayHelper::merge($model, ArrayHelper::getValue(Yii::$app->request->post(), 'qr'));
			if ($model->validate())
				$model->generateQr();
			$qrForm = $this->renderPartial('qr/' . strtolower($model->type), [
				'model' => $model
			]);
		}

		return $this->render('qr', [
			'model' => $model,
			'qrForm' => $qrForm
		]);
	}
}
