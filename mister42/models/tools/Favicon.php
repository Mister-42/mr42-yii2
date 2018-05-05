<?php
namespace app\models\tools;
use Yii;
use app\models\Mailer;

class Favicon extends \yii\base\Model {
	public $recipient;
	public $sourceImage;
	public $dimensions = [16, 32, 48, 64];

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
		if (!$this->validate()) {
			Yii::$app->getSession()->setFlash('favicon-error', 'Validation failed. Please upload a valid image.');
			return false;
		}

		$rndFilename = uniqid('favicon');
		$srcImg = $this->sourceImage->tempName;
		list($width, $height) = getimagesize($srcImg);

		if (empty($width)) {
			Yii::$app->getSession()->setFlash('favicon-error', 'Uploaded file could not be converted. Make sure you upload a valid image.');
			unlink($srcImg);
			return false;
		}

		$tmpSize = min(500, $width, $height);
		exec("convert {$srcImg} -resize \"{$tmpSize}x{$tmpSize}^\" -gravity center -crop {$tmpSize}x{$tmpSize}+0+0 +repage {$srcImg}");

		foreach ($this->dimensions as $dimension) :
			$tmpFiles[] = Yii::getAlias("@assetsroot/temp/{$rndFilename}.{$dimension}.png");
			exec("convert -scale {$dimension} {$srcImg} ".end($tmpFiles));
		endforeach;
		exec('convert '.implode(' ', $tmpFiles).' '.Yii::getAlias("@assetsroot/temp/{$rndFilename}.ico"));
		foreach ($tmpFiles as $file)
			unlink($file);
		unlink($srcImg);

		if ($this->recipient)
			Mailer::sendFileHtml($this->recipient, 'Your favicon from '.Yii::$app->name, 'faviconRequester', ['file' => "@assetsroot/temp/{$rndFilename}.ico", 'name' => 'favicon.ico']);
		Yii::$app->getSession()->setFlash('favicon-success', $rndFilename.'.ico');
		return true;
	}
}
