<?php

namespace mister42\assets;

use Yii;
use yii\web\AssetBundle;
use yii\web\View;

class Html2MarkdownAsset extends AssetBundle
{
    public $depends = [
        'mister42\assets\AppAsset',
    ];

    public $js = [
        'turndown.js',
    ];

    public $sourcePath = '@npm/turndown/dist';

    public function registerAssetFiles($view): void
    {
        parent::registerAssetFiles($view);
        Yii::$app->view->registerJs(Yii::$app->formatter->jspack('tools/html-to-markdown.js'), View::POS_READY);
    }
}
