<?php

namespace mister42\assets;

use yii\web\AssetBundle;

class LightboxAsset extends AssetBundle
{
    public $css = [
        'css/lightbox.min.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public $js = [
        'js/lightbox.min.js',
    ];

    public $sourcePath = '@npm/lightbox2/dist';
}
