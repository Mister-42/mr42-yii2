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
			[['recipient'], 'email', 'checkDNS' => true, 'enableIDN' => true],
			[['sourceImage'], 'file',
				'minSize' => 64,
				'maxSize' => 1024 * 1024 * 2.5,
				'skipOnEmpty' => false,
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'recipient' => 'Email Address',
			'generate' => 'Convert Image',
		];
	}

	public function convertImage(): bool {
		if (!file_exists(Yii::getAlias('@assetsroot/temp')))
			FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));

		list($width, $height) = getimagesize($this->sourceImage->tempName);
		if (!$this->validate() || empty($width) || empty($height)) {
			if (!$this->validate())
				Yii::$app->getSession()->setFlash('favicon-error', 'Validation failed. Please upload a valid image.');
			elseif (empty($width) || empty($height))
				Yii::$app->getSession()->setFlash('favicon-error', 'Uploaded file could not be converted. Make sure you upload a valid image.');
			FileHelper::unlink($this->sourceImage->tempName);
			return false;
		}

		$tmpSize = min($width, $height);
		exec("convert {$this->sourceImage->tempName} -gravity center -crop {$tmpSize}x{$tmpSize}+0+0 +repage {$this->sourceImage->tempName}");

		$rndFilename = uniqid('favicon');
		exec("convert {$this->sourceImage->tempName} -define icon:auto-resize=" . implode(',', $this->dimensions) . ' ' . Yii::getAlias("@assetsroot/temp/{$rndFilename}.ico"));
		FileHelper::unlink($this->sourceImage->tempName);

		if ($this->recipient)
			Mailer::sendFileHtml($this->recipient, 'Your favicon from '.Yii::$app->name, 'faviconRequester', ['file' => "@assetsroot/temp/{$rndFilename}.ico", 'name' => 'favicon.ico']);
		Yii::$app->getSession()->setFlash('favicon-success', $rndFilename.'.ico');
		return true;
	}
}
