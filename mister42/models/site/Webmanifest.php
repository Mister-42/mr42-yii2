<?php

namespace mister42\models\site;

use Yii;
use yii\helpers\Url;

class Webmanifest
{
    public static function getData(): array
    {
        return [
            'name' => Yii::$app->name,
            'short_name' => Yii::$app->name,
            'description' => Yii::t('mr42', 'Sharing beautiful knowledge of the world.'),
            'icons' => self::getIcons(['android-chrome-192x192.png', 'android-chrome-512x512.png']),
            'theme_color' => Yii::$app->params['themeColor'],
            'background_color' => '#FFFFFF',
            'start_url' => '/',
            'display' => 'standalone',
        ];
    }

    private static function getIcons(array $files): array
    {
        foreach ($files as $icon) {
            $size = getimagesize(Yii::getAlias("@assetsroot/images/{$icon}"));
            $icons[] = [
                'src' => Url::to("@assets/images/{$icon}", Yii::$app->request->isSecureConnection ? 'https' : 'http'),
                'sizes' => $size[0] . 'x' . $size[1],
                'type' => $size['mime'],
            ];
        }
        return $icons;
    }
}
