<?php

namespace app\assets;

use Yii;
use yii\bootstrap4\Html;
use yii\helpers\Json;
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
