<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class HighlightAsset extends AssetBundle
{
    public $css = [
        'styles/default.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public $js = [
        'highlight.pack' . (YII_DEBUG ? '' : '.min') . '.js',
    ];

    public $sourcePath = '@npm/highlightjs/';

    public static function register($view)
    {
        $view->registerJs('hljs.initHighlightingOnLoad();', View::POS_END);

        return parent::register($view);
    }
}
