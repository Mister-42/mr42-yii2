<?php

namespace app\controllers;

use app\assets\HighlightAsset;
use app\apidoc\ApiRenderer;
use app\components\object\ClassType;
use app\models\Doc;
use app\models\Extension;
use Yii;
use yii\helpers\Html;
use yii\filters\HttpCache;
use yii\helpers\StringHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnsupportedMediaTypeHttpException;
use yii\filters\ContentNegotiator;

class ExtensionsController extends \yii\web\Controller
{
    private $name;

    public function actionIndex($name)
    {
        return $this->actionView($name, 'index');
    }

    public function actionView($name, $section)
    {
        if (!preg_match('/^[\w\-]+$/', $section)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }

        $title = $section;
        $file = Yii::getAlias("@app/../html/{$name}/{$section}.html");

        if (!is_file($file)) {
            return $this->extension404($name, $section);
        }

        return $this->render('view', [
                    'content' => file_get_contents($file),
                    'name' => $name,
                    'section' => $section,
                    'title' => $title,
                ]);
    }

    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
            ],
        ];
    }
}
