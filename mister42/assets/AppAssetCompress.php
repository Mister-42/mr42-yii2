<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAssetCompress extends AssetBundle
{
    public $css = [
        'site.scss',
    ];

    public $js = [
    ];

    public $sourcePath = '@app/assets/css';
}
