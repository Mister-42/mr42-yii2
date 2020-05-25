<?php

namespace mister42\controllers;

use Yii;
use yii\filters\ContentNegotiator;
use yii\web\NotFoundHttpException;

class ExtensionsController extends \yii\web\Controller
{
    private string $name;

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
