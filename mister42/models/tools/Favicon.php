<?php
namespace app\models\tools;
use Yii;
use app\models\Mailer;
use yii\helpers\FileHelper;

class Favicon extends \yii\base\Model {
	public $recipient;
	public $sourceImage;
	public $dimensions = [64, 48, 32, 16];

	public function rules(): array {
		return [
			['recipient', 'email', 'checkDNS' => true, 'enableIDN' => true],
			['sourceImage', 'required'],
			['sourceImage', 'image', 'maxHeight' => 2500, 'maxWidth' => 2500, 'minHeight' => 256, 'minWidth' => 256],
		];
	}

	public function attributeLabels(): array {
		return [
			'recipient' => 'Email Address',
		];
	}

	public function convertImage(): bool {
		if (!$this->validate()) {
			Yii::$app->getSession()->setFlash('favicon-error', 'Uploaded file could not be converted. Make sure you upload a valid image.');
			FileHelper::unlink($this->sourceImage->tempName);
			return false;
		}

		list($width, $height) = getimagesize($this->sourceImage->tempName);
		$tmpSize = min($width, $height);
		FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));
		$cacheFile = Yii::getAlias('@assetsroot/temp/'.uniqid('favicon').'.ico');
		exec("convert {$this->sourceImage->tempName} -gravity center -crop {$tmpSize}x{$tmpSize}+0+0 +repage -resize 256x256 -define icon:auto-resize=".implode(',', $this->dimensions)." {$cacheFile}");
		FileHelper::unlink($this->sourceImage->tempName);

		if ($this->recipient) {
					Mailer::sendFileHtml($this->recipient, 'Your favicon from '.Yii::$app->name, 'faviconRequester', ['file' => $cacheFile, 'name' => 'favicon.ico']);
		}
		Yii::$app->getSession()->setFlash('favicon-success', basename($cacheFile));
		return true;
	}
}
