<?php
namespace app\controllers;
use Phar;
use PharData;
use Yii;
use yii\helpers\FileHelper;
use yii\web\HttpException;

class DownloadController extends \yii\web\Controller {
	public function actionPhp($version) {
		if (!file_exists(Yii::getAlias('@assetsroot/temp')))
			FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));

		$phpFile = Yii::getAlias("@webroot/../../../bin/php{$version}-cli");
		$archiveFile = Yii::getAlias('@assetsroot/temp/') . uniqid("php{$version}-") . '.tar';
		$compressedFile = $archiveFile . '.bz2';

		if (!is_readable($phpFile))
			throw new HttpException(404, 'The requested file could not be found.');

		$a = new PharData($archiveFile);
		$a->addFile(Yii::getAlias("@webroot/../../../bin/php{$version}-cli"), "bin/php{$version}-cli");
		$a->addFile(Yii::getAlias('@webroot/../../../bin/lib/libcrypto.so.1.1'), 'bin/lib/libcrypto.so.1.1');
		$a->addFile(Yii::getAlias('@webroot/../../../bin/lib/libpng16.so.16'), 'bin/lib/libpng16.so.16');
		$a->addFile(Yii::getAlias('@webroot/../../../bin/lib/libssl.so.1.1'), 'bin/lib/libssl.so.1.1');
		$a->convertToData(Phar::TAR, Phar::BZ2);
		unlink($archiveFile);

		Yii::$app->response->sendFile($compressedFile, "php{$version}.tar.bz2");
	}
}
