<?php
use Yii;
use yii\helpers\{Html, Url};

$this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
	<title><?= $this->title ?></title>
	<?php $this->head() ?>
</head>
<body>
	<?php $this->beginBody() ?>
	<?= Html::tag('div', $content, ['class' => 'mail']) ?>
	<p>Thank you for visiting <?= Yii::$app->name ?><br>
	<?php list($width, $height, $type, $attr) = getimagesize(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/logo.png'); ?>
	<?= Html::a('<img src="'.$message->embed(Yii::$app->assetManager->getBundle('app\assets\ImagesAsset')->basePath.'/logo.png').'" alt="'.Yii::$app->name.'" '.$attr.'>', Url::home(true)) ?><br>
	<?= Html::a(Url::home(true), Url::home(true)) ?></p>
	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
