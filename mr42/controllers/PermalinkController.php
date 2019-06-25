<?php

namespace app\controllers;

use app\models\Articles;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class PermalinkController extends \yii\web\Controller
{
    public function actionArticles($id): void
    {
        $article = Articles::find()->select(['id', 'title', 'url'])->where(['id' => $id]);
        if ((int) $article->count() === 0) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $article = $article->one();
        $this->redirect(Url::to(['articles/article', 'id' => $article->id, 'title' => $article->url]), 301)->send();
    }
}
