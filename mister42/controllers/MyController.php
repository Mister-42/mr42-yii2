<?php

namespace mister42\controllers;

use mister42\models\my\Contact;
use Yii;
use yii\base\BaseObject;
use yii\filters\HttpCache;
use yii\web\UploadedFile;

class MyController extends \yii\web\Controller
{
    public function actionContact(): string
    {
        $model = new Contact();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->attachment = UploadedFile::getInstance($model, 'attachment');
            if ($model->sendEmail()) {
                return $this->renderAjax('contact-success');
            }
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionPi(): string
    {
        return $this->render('pi');
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => HttpCache::class,
                'enabled' => !YII_DEBUG,
                'etagSeed' => function (BaseObject $action) {
                    $file = "@app/views/{$action->controller->id}/{$action->id}.php";
                    return serialize([phpversion(), Yii::$app->user->id, Yii::$app->view->renderFile($file)]);
                },
                'lastModified' => function (BaseObject $action) {
                    return filemtime(Yii::getAlias("@app/views/{$action->controller->id}/{$action->id}.php"));
                },
                'except' => ['contact'],
            ],
        ];
    }
}
