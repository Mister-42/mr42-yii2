<?php

$sass = 'sassc --style compressed --load-path ' . Yii::getAlias('@npm/bootstrap/scss') . ' {from} {to}';

return [
    'bundles' => [
        'mister42\assets\AppAssetCompress',
    ],
    'cssCompressor' => $sass,
    'deleteSource' => true,
    'targets' => [
        'all' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@runtime/assets',
            'baseUrl' => '@web/assets',
            'css' => 'css/site.css',
        ],
    ],
    'assetManager' => [
        'basePath' => '@runtime/assets',
        'baseUrl' => '@web/assets',
        'converter' => [
            'class' => 'yii\web\AssetConverter',
            'commands' => [
                'scss' => ['css', $sass],
            ],
        ],
    ],
];
