<?php
namespace app\controllers;
use Yii;
use Phar;
use PharData;
use yii\helpers\FileHelper;
use yii\web\HttpException;

class DownloadController extends \yii\web\Controller {
	public function actionPhp($version) {
		$phpFile = Yii::getAlias("@webroot/../../../bin/php{$version}-cli");
		$archiveFile = Yii::getAlias("@assetsroot/php/php{$version}.tar");
		$compressedFile = $archiveFile . '.bz2';

		if (!file_exists(dirname($archiveFile)))
			FileHelper::createDirectory(dirname($archiveFile));

		if (!is_readable($phpFile))
			throw new HttpException(404, 'The requested file could not be found.');

		if (!file_exists($compressedFile) || filemtime($compressedFile) < filemtime($phpFile)) {
			if (file_exists($compressedFile))
				unlink($compressedFile);
			$a = new PharData($archiveFile);
			$a->addFile(Yii::getAlias("@webroot/../../../bin/php{$version}-cli"), "bin/php{$version}-cli");
			$a->addFile(Yii::getAlias('@webroot/../../../bin/lib/libcrypto.so.1.1'), 'bin/lib/libcrypto.so.1.1');
			$a->addFile(Yii::getAlias('@webroot/../../../bin/lib/libpng16.so.16'), 'bin/lib/libpng16.so.16');
			$a->addFile(Yii::getAlias('@webroot/../../../bin/lib/libssl.so.1.1'), 'bin/lib/libssl.so.1.1');
			$a->convertToData(Phar::TAR, Phar::BZ2);
			touch($compressedFile, filemtime($phpFile));
			unlink($archiveFile);
		}
		Yii::$app->response->redirect('https://s.mister42.me/php/' . basename($compressedFile))->send();
	}
}
