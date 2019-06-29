<?php

namespace app\controllers;

use app\models\tools\Barcode;
use app\models\tools\Favicon;
use app\models\tools\Oui;
use app\models\tools\PhoneticAlphabet;
use app\models\tools\Qr;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ToolsController extends \yii\web\Controller
{
    public function actionBarcode()
    {
        $model = new Barcode();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->generate();
        }

        return $this->render('barcode', [
            'model' => $model,
        ]);
    }

    public function actionFavicon()
    {
        $model = new Favicon();
        if ($model->load(Yii::$app->request->post())) {
            $model->sourceImage = UploadedFile::getInstance($model, 'sourceImage');
            if ($model->convertImage()) {
                return $this->refresh();
            }
        }

        return $this->render('favicon', [
            'model' => $model,
        ]);
    }

    public function actionHeaders()
    {
        return $this->render('headers');
    }

    public function actionHtmlToMarkdown()
    {
        return $this->render('html-to-markdown');
    }

    public function actionOui()
    {
        $model = new Oui();
        $model->load(Yii::$app->request->post());

        return $this->render('oui', [
            'model' => $model,
        ]);
    }

    public function actionPassword()
    {
        return $this->render('password');
    }

    public function actionPhoneticAlphabet()
    {
        $model = new PhoneticAlphabet();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->convertText();
        }

        return $this->render('phonetic-alphabet', [
            'model' => $model,
        ]);
    }

    public function actionQr()
    {
        $model = new Qr();
        if (Yii::$app->request->isPost) {
            $type = ArrayHelper::getValue(Yii::$app->request->post(), 'type') ?? ArrayHelper::getValue(Yii::$app->request->post(), 'qr.type');
            if (!in_array($type, $model->getTypes(true))) {
                throw new NotFoundHttpException('Type ' . $type . ' not found.');
            }
            $modelName = "\\app\\models\\tools\\qr\\{$type}";
            $model = new $modelName();
            $model->type = $type === 'SMS' ? 'Phone' : $type;

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('qr/' . mb_strtolower($model->type), [
                    'model' => $model,
                ]);
            }

            $model = ArrayHelper::merge($model, ArrayHelper::getValue(Yii::$app->request->post(), 'qr'));
            if ($model->validate()) {
                $model->generateQr();
            }

            $qrForm = $this->renderPartial('qr/' . mb_strtolower($model->type), [
                'model' => $model,
            ]);
        }

        return $this->render('qr', [
            'model' => $model,
            'qrForm' => $qrForm ?? '',
        ]);
    }
    public function behaviors()
    {
        return [
            [
                'class' => \yii\filters\HttpCache::class,
                'enabled' => !YII_DEBUG,
                'etagSeed' => function (BaseObject $action) {
                    return serialize([phpversion(), Yii::$app->user->id, Yii::$app->view->renderFile("@app/views/{$action->controller->id}/{$action->id}.php")]);
                },
                'lastModified' => function (BaseObject $action) {
                    return filemtime(Yii::getAlias('@app/views/' . $action->controller->id . '/' . $action->id . '.php'));
                },
                'only' => ['html-to-markdown', 'password'],
            ],
        ];
    }
}
