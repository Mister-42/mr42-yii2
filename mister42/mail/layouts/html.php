<?php

use Yii;
use yii\helpers\Html;

$this->beginPage();
echo Html::beginTag('!DOCTYPE', ['html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"' => true]);
echo Html::beginTag('html', ['xmlns' => 'http://www.w3.org/1999/xhtml']);
echo Html::beginTag('head');
    echo Html::tag('meta', null, ['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=' . Yii::$app->charset]);
    echo Html::tag('title', $this->title);
    $this->head();
echo Html::endTag('head');
echo Html::beginTag('body');
$this->beginBody();
    echo Html::tag('div', $content, ['class' => 'mail']);
    echo Html::tag('p', Yii::t('mr42', 'Thank you for visiting {website}.', ['website' => Yii::$app->name]));
    [,,, $attr] = getimagesize(Yii::getAlias('@assetsroot/images/mr42.png'));
    echo Html::a('<img src="' . $message->embed(Yii::getAlias('@assetsroot/images/mr42.png')) . '" alt="' . Yii::$app->name . '" ' . $attr . '><br>' . Yii::$app->params['shortDomain'], Yii::$app->params['shortDomain']);
$this->endBody();
echo Html::endTag('body');
echo Html::endTag('html');
$this->endPage();
