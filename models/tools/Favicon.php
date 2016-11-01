<?php
namespace app\models\tools;
use Imagick;
use ImagickException;
use Yii;

class Favicon extends \yii\base\Model {
	public $email;
	public $sourceImage;
	public $dimensions = [16, 32, 48, 64];

	public function rules() {
		return [
			[['email'], 'email', 'checkDNS' => true, 'enableIDN' => true],
			[['sourceImage'], 'file',
				'minSize' => 64,
				'maxSize' => 1024 * 1024 * 2.5,
				'skipOnEmpty' => false,
			],
		];
	}

	public function attributeLabels() {
		return [
			'email' => 'Email Address',
			'sourceImage' => 'Source Image',
			'generate' => 'Convert Image',
		];
	}

	public function convertImage() {
		if ($this->validate()) {
			try {
				$rndFilename = uniqid('favicon');

				if (extension_loaded('imagick')) {
					$srcImg = new Imagick($this->sourceImage->tempName);
					$icon = new Imagick();
					$icon->setFormat('ico');

					$geo = $srcImg->getImageGeometry();
					$tmpSize = min(640, $geo['width'], $geo['height']);
					($tmpSize / $geo['width'] * $geo['height'] > $tmpSize) ? $srcImg->scaleImage($tmpSize, 0) : $srcImg->scaleImage(0, $tmpSize);
					$srcImg->cropThumbnailImage($tmpSize, $tmpSize);

					foreach ($this->dimensions as $dimension) {
						$clone = clone $srcImg;
						$clone->scaleImage($dimension, 0);
						$icon->addImage($clone);
						$clone->clear();
					}
					$srcImg->clear();
					$icon->writeImages(Yii::getAlias('@webroot/assets/temp/favicon/'.$rndFilename.'.ico'), true);
					$icon->clear();
				} else {
					$srcImg = $this->sourceImage->tempName;
					$geo['width'] = exec('convert ' . $srcImg .  ' -print "%w"');
					$geo['height'] = exec('convert ' . $srcImg .  ' -print "%h"');

					if (empty($geo['width'])) {
						Yii::$app->getSession()->setFlash('favicon-error', 'Uploaded file could not be converted. Make sure you upload a valid image.');
						unlink($srcImg);
						return false;
					}

					$tmpSize = min(640, $geo['width'], $geo['height']);
					($tmpSize / $geo['width'] * $geo['height'] > $tmpSize) ? exec('convert -scale '.$tmpSize.',0'.$srcImg) : exec('convert -scale 0,'.$tmpSize.' '.$srcImg);
					exec('convert -crop '.$tmpSize.'x'.$tmpSize.' '.$srcImg);

					foreach ($this->dimensions as $dimension) {
						$tmpFiles[] = Yii::getAlias('@webroot/assets/temp/'.$rndFilename.'.'.$dimension.'.png');
						exec('convert -scale '.$dimension.' '.$srcImg.' '.end($tmpFiles));
					}
					exec('convert '.implode(' ', $tmpFiles).' '.Yii::getAlias('@webroot/assets/temp/favicon/'.$rndFilename.'.ico'));
					foreach ($tmpFiles as $file)
						unlink($file);
					unlink($srcImg);
				}
			
				if ($this->email) {
					Yii::$app->mailer
						->compose(['html' => 'faviconRequester'])
						->setTo($this->email)
						->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name])
						->setSubject('Your favicon file from '.Yii::$app->name)
						->attach(Yii::getAlias('@webroot/assets/temp/favicon/'.$rndFilename.'.ico'), ['fileName' => 'favicon.ico'])
						->send();
				}
				Yii::$app->getSession()->setFlash('favicon-success', $rndFilename.'.ico');
				return true;
			} catch(ImagickException $e) {
				Yii::$app->getSession()->setFlash('favicon-error', 'Uploaded file could not be converted. Make sure you upload a valid image.');
				return false;
			}
		}

		Yii::$app->getSession()->setFlash('favicon-error', 'Validation failed. Please upload a valid image.');
		return false;
	}
}
