<?php
use yii\helpers\Url;

$doc = new XMLWriter();
$doc->openMemory();
$doc->setIndent(YII_DEBUG);

$doc->startDocument('1.0', 'UTF-8');

$doc->startElement('browserconfig');
	$doc->startElement('msapplication');
		$doc->startElement('tile');
			$doc->startElement('square70x70logo');
				$doc->writeAttribute('src', Url::to(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/mstile-310x310.png', Yii::$app->request->isSecureConnection ? 'https' : 'http'));
			$doc->endElement();
			$doc->startElement('square150x150logo');
				$doc->writeAttribute('src', Url::to(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/mstile-310x310.png', Yii::$app->request->isSecureConnection ? 'https' : 'http'));
			$doc->endElement();
			$doc->startElement('square310x310logo');
				$doc->writeAttribute('src', Url::to(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->baseUrl.'/mstile-310x310.png', Yii::$app->request->isSecureConnection ? 'https' : 'http'));
			$doc->endElement();
			$doc->writeElement('TileColor', Yii::$app->params['themeColor']);
		$doc->endElement();
		$doc->startElement('notification');
			$doc->startElement('polling-uri');
				$doc->writeAttribute('src', Url::to(['/feed/rss'], true));
			$doc->endElement();
			$doc->writeElement('frequency', 30);
		$doc->endElement();
	$doc->endElement();
$doc->endElement();

$doc->endDocument();
echo $doc->outputMemory();
