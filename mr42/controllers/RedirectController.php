<?php
namespace app\controllers;
use yii\helpers\Url;
use yii\web\Controller;

class RedirectController extends Controller {
	public function actionIndex() {
		$this->redirect('https://www.mister42.me' . Url::to(), 301)->send();
	}
}
