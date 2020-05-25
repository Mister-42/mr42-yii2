<?php

namespace mr42\controllers;

use mister42\models\articles\Articles;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ArticlesController extends \yii\web\Controller
{
    public function actionPdf(int $id, string $title): Response
    {
        $model = Articles::find()
            ->where(['id' => $id])
            ->one();

        if (!$model->pdf) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        if ($title !== $model->url) {
            $url = Yii::$app->mr42->createUrl(['articles/pdf', 'id' => $model->id, 'title' => $model->url]);
            $this->redirect($url, 301)->send();
        }

        $fileName = Articles::buildPdf($model);
        return Yii::$app->response->sendFile($fileName, implode(' - ', [Yii::$app->name, $model->url]) . '.pdf');
    }
}
