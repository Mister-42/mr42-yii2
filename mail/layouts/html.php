<?php
use Yii;
use yii\helpers\BaseUrl;
use yii\helpers\Html;

$this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body>
	<?php $this->beginBody() ?>
	<div class="mail">
	<?= $content ?>
	</div>

	<p>Thank you for visiting <?= Html::encode(Yii::$app->name) ?><br />
	<?= Html::a('<img src="'.$message->embed(Yii::getAlias('@assetsPath/images/logo.png')).'" alt="'.Html::encode(Yii::$app->name).'" height="30" width="144">', BaseUrl::home(true)) ?><br />
	<?= Html::a(BaseUrl::home(true), BaseUrl::home(true)) ?></p>
	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
