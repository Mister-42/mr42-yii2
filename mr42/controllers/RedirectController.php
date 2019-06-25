<?php

namespace app\controllers;

use yii\helpers\Url;

class RedirectController extends \yii\web\Controller
{
    public function actionIndex(): void
    {
        $params = (new \mister42\Params())->getValues();
        $this->redirect($params['longDomain'] . Url::to(), 301)->send();
    }
}
