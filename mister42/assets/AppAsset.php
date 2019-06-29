<?php

namespace app\assets;

use Yii;
use yii\web\AssetBundle;
use yii\web\View;

class AppAsset extends AssetBundle
{
    public $css = [
        'site.css',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];

    public $js = [
    ];

    public $sourcePath = '@runtime/assets/css';

    public function registerAssetFiles($view): void
    {
        parent::registerAssetFiles($view);
        $view->registerJs(Yii::$app->formatter->jspack('scrolltop.js'), View::POS_READY);
        $view->registerJs(Yii::$app->formatter->jspack('targetBlank.js'), View::POS_READY);
        $view->registerJs('$(function(){$(\'[data-toggle="tooltip"]\').tooltip()});', View::POS_READY);
    }
}
