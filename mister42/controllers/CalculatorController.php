<?php

namespace mister42\controllers;

use mister42\models\calculator\Date;
use mister42\models\calculator\Duration;
use mister42\models\calculator\Office365;
use mister42\models\calculator\Timezone;
use Yii;
use yii\base\BaseObject;
use yii\filters\HttpCache;

class CalculatorController extends \yii\web\Controller
{
    public function actionDate()
    {
        $model = new Date();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->calculate()) {
            #			return $this->refresh();
        }

        return $this->render('date', [
            'model' => $model,
        ]);
    }

    public function actionDuration()
    {
        $model = new Duration();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->calculate()) {
            #			return $this->refresh();
        }

        return $this->render('duration', [
            'model' => $model,
        ]);
    }

    public function actionOffice365()
    {
        $model = new Office365();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->calculate()) {
            #			return $this->refresh();
        }

        return $this->render('office365', [
            'model' => $model,
        ]);
    }

    public function actionTimezone()
    {
        $model = new Timezone();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->calculate()) {
            #			return $this->refresh();
        }

        return $this->render('timezone', [
            'model' => $model,
        ]);
    }

    public function actionWeeknumbers()
    {
        return $this->render('weeknumbers');
    }

    public function actionWpapsk()
    {
        return $this->render('wpapsk');
    }

    public function behaviors()
    {
        return [
            [
                'class' => HttpCache::class,
                'enabled' => !YII_DEBUG,
                'etagSeed' => function (BaseObject $action) {
                    return serialize([phpversion(), Yii::$app->user->id, Yii::$app->view->renderFile("@app/views/{$action->controller->id}/{$action->id}.php")]);
                },
                'lastModified' => function (BaseObject $action) {
                    return filemtime(Yii::getAlias('@app/views/' . $action->controller->id . '/' . $action->id . '.php'));
                },
                'only' => ['wpapsk'],
            ],
        ];
    }
}
