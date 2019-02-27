<?php
use Yii;
use yii\helpers\Html;

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
	<?php [$width, $height, $type, $attr] = getimagesize(Yii::getAlias('@assetsroot/images/mr42.png')); ?>
	<?= Html::a('<img src="'.$message->embed(Yii::getAlias('@assetsroot/images/mr42.png')).'" alt="'.Yii::$app->name.'" '.$attr.'>', Yii::$app->params['shortDomain']) ?><br>
	<?= Html::a(Yii::$app->params['shortDomain'], Yii::$app->params['shortDomain']) ?></p>
	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
