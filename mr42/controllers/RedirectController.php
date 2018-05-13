<?php
namespace app\controllers;
use yii\helpers\Url;

class RedirectController extends \yii\web\Controller {
	public function actionIndex() {
		$this->redirect('https://www.mister42.me'.Url::to(), 301)->send();
	}
}
