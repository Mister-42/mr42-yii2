<?php

namespace app\assets;

use yii\web\AssetBundle;

class LightboxAsset extends AssetBundle
{
    public $sourcePath = '@npm/lightbox2/dist';

    public $js = [
        'js/lightbox.min.js',
    ];

    public $css = [
        'css/lightbox.min.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
