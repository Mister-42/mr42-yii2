<?php

namespace mister42\assets;

use Yii;
use yii\bootstrap4\Html;
use yii\web\AssetBundle;
use yii\web\View;

class CharCounterAsset extends AssetBundle
{
    public static function register($view, int $chars = 1024)
    {
        $options = [
            'chars' => $chars,
            'lang' => [
                'overLimit' => Yii::t('mr42', '{x} characters over the limit', ['x' => Html::tag('span', null, ['class' => 'charcount'])]),
                'charsLeft' => Yii::t('mr42', '{x} characters left', ['x' => Html::tag('span', null, ['class' => 'charcount'])]),
            ],
        ];

        $view->registerJsVar('formCharCount', $options, View::POS_READY);
        Yii::$app->view->registerJs(Yii::$app->formatter->jspack('formCharCounter.js'), View::POS_READY);

        return parent::register($view);
    }
}
