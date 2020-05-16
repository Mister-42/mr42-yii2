<?php

namespace mister42\assets;

use Yii;
use yii\web\AssetBundle;
use yii\web\View;

class ClipboardJsAsset extends AssetBundle
{
    public $depends = [
        'mister42\assets\AppAsset',
    ];

    public $js = [
        'clipboard.min.js',
    ];

    public $sourcePath = '@vendor/zenorocha/clipboardjs/dist';

    public function registerAssetFiles($view): void
    {
        parent::registerAssetFiles($view);
        Yii::$app->view->registerJs("var genpass = {lang:{copied:'" . Yii::t('mr42', 'Copied') . "', copy:'" . Yii::t('mr42', 'Copy to Clipboard') . "'}};" . Yii::$app->formatter->jspack('clipboard.js'), View::POS_READY);
    }
}
