<?php
use yii\bootstrap4\Html;
use Yii;

$this->title = Yii::t('mr42', 'Error {statusCode}', ['statusCode' => Yii::$app->response->statusCode]);

echo Html::tag('h1', $this->title);
echo Html::tag('div', nl2br($message, false), ['class' => 'alert alert-danger']);
