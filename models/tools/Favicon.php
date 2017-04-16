<?php
namespace app\models\tools;
use Yii;

class Favicon extends \yii\base\Model {
	public $email;
	public $sourceImage;
	public $dimensions = [16, 32, 48, 64];

	public function rules(): array {
		return [
			[['email'], 'email', 'checkDNS' => true, 'enableIDN' => true],
			[['sourceImage'], 'file',
				'minSize' => 64,
				'maxSize' => 1024 * 1024 * 2.5,
				'skipOnEmpty' => false,
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'email' => 'Email Address',
			'sourceImage' => 'Source Image',
			'generate' => 'Convert Image',
		];
	}

	public function convertImage(): bool {
		if ($this->validate()) {
			$rndFilename = uniqid('favicon');
			$srcImg = $this->sourceImage->tempName;
			list($width, $height) = getimagesize($srcImg);

			if (empty($width)) {
				Yii::$app->getSession()->setFlash('favicon-error', 'Uploaded file could not be converted. Make sure you upload a valid image.');
				unlink($srcImg);
				return false;
			}

			$tmpSize = min(640, $width, $height);
			$tmpSize / $width * $height > $tmpSize ? exec("convert -scale {$tmpSize},0 {$srcImg}") : exec('convert -scale 0,'.$tmpSize.' '.$srcImg);
			exec("convert -crop {$tmpSize}x{$tmpSize} {$srcImg}");

			foreach ($this->dimensions as $dimension) :
				$tmpFiles[] = Yii::getAlias("@webroot/assets/temp/{$rndFilename}.{$dimension}.png");
				exec("convert -scale {$dimension} {$srcImg} ".end($tmpFiles));
			endforeach;
			exec('convert '.implode(' ', $tmpFiles).' '.Yii::getAlias("@webroot/assets/temp/favicon/{$rndFilename}.ico"));
			foreach ($tmpFiles as $file)
				unlink($file);
			unlink($srcImg);

			if ($this->email)
				Yii::$app->mailer
					->compose(['html' => 'faviconRequester'])
					->setTo($this->email)
					->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name])
					->setSubject('Your favicon file from '.Yii::$app->name)
					->attach(Yii::getAlias("@webroot/assets/temp/favicon/{$rndFilename}.ico"), ['fileName' => 'favicon.ico'])
					->send();
			Yii::$app->getSession()->setFlash('favicon-success', $rndFilename.'.ico');
			return true;
		}

		Yii::$app->getSession()->setFlash('favicon-error', 'Validation failed. Please upload a valid image.');
		return false;
	}
}
