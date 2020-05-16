<?php

namespace mister42\assets;

use yii\web\AssetBundle;
use yii\web\View;

class FileInputAsset extends AssetBundle
{
    public $js = [
        'bs-custom-file-input.min.js',
    ];

    public $sourcePath = '@npm/bs-custom-file-input/dist';

    public function registerAssetFiles($view): void
    {
        parent::registerAssetFiles($view);
        $view->registerJs("bsCustomFileInput.init();", View::POS_READY);
    }
}
