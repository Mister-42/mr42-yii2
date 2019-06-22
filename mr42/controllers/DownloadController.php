<?php
namespace app\controllers;
use Phar;
use PharData;
use Yii;
use yii\helpers\FileHelper;
use yii\web\HttpException;

class DownloadController extends \yii\web\Controller {
	public function actionPhp($version) {
		FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));
		$phpFile = Yii::getAlias("@webroot/../../../bin/php{$version}-cli");
		$archiveFile = Yii::getAlias('@assetsroot/temp/').uniqid("php{$version}-").'.tar';
		$compressedFile = $archiveFile.'.bz2';

		if (!is_readable($phpFile))
			throw new HttpException(404, 'The requested file could not be found.');

		$a = new PharData($archiveFile);
		$a->addFile(Yii::getAlias("@webroot/../../../bin/php{$version}-cli"), "bin/php{$version}-cli");
		foreach (['libcrypto.so.1.1', 'libicudata.so.57', 'libicui18n.so.57', 'libicuio.so.57', 'libicuuc.so.57', 'libonig.so.4', 'libpng16.so.16', 'libssl.so.1.1', 'libzip.so.4'] as $file)
			$a->addFile(Yii::getAlias("@webroot/../../../bin/lib/{$file}"), "bin/lib/{$file}");
		$a->convertToData(Phar::TAR, Phar::BZ2);
		FileHelper::unlink($archiveFile);

		Yii::$app->response->sendFile($compressedFile, "php{$version}.tar.bz2");
	}
}
