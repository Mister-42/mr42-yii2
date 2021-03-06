<?php

namespace mr42\controllers;

use mister42\models\articles\Articles;
use mister42\Secrets;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\Response;

class FeedController extends \yii\web\Controller
{
    public function actionRss(): string
    {
        $secrets = (new Secrets())->getValues();
        if (php_sapi_name() !== 'cli' && !StringHelper::startsWith(Yii::$app->request->headers->get('user-agent'), 'FeedBurner') && !ArrayHelper::isIn(Yii::$app->request->userIP, $secrets['params']['specialIPs'])) {
            $this->redirect('https://feeds.feedburner.com/Mr42')->send();
        }

        $articles = Articles::find()
            ->orderBy(['updated' => SORT_DESC])
            ->with('author')
            ->limit(5)
            ->all();

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/rss+xml');
        return $this->renderPartial('rss', [
            'articles' => $articles,
        ]);
    }

    public function actionSitemap(): string
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/xml');
        return $this->renderPartial(Yii::$app->controller->action->id);
    }

    public function actionSitemapArticles(): string
    {
        return $this->actionSitemap();
    }

    public function actionSitemapLyrics(): string
    {
        return $this->actionSitemap();
    }
}
