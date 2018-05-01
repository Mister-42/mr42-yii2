<?php
namespace app\models\site;
use Yii;
use yii\helpers\Url;

class Webmanifest {
	public static function getData(): array {
		return [
			'name' => Yii::$app->name,
			'short_name' => Yii::$app->name,
			'description' => Yii::$app->params['description'],
			'icons' => self::getIcons(['android-chrome-192x192.png', 'android-chrome-512x512.png']),
			'theme_color' => Yii::$app->params['themeColor'],
			'background_color' => '#FFFFFF',
			'start_url' => Yii::$app->params['shortDomain'],
			'display' => 'standalone',
		];
	}

	private function getIcons(array $files): array {
		foreach ($files as $icon) :
			$size = getimagesize(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/'.$icon);
			$icons[] = [
				'src' => Url::to(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/' . $icon, Yii::$app->request->isSecureConnection ? 'https' : 'http'),
				'sizes' => $size[0] . 'x' . $size[1],
				'type' => $size['mime'],
			];
		endforeach;
		return $icons;
	}
}
