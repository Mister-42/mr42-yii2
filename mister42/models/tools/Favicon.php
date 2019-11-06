<?php

namespace app\models\tools;

use app\models\Mailer;
use Imagick;
use Yii;
use yii\helpers\FileHelper;

class Favicon extends \yii\base\Model
{
    public $dimensions = [64, 48, 32, 16];
    public $recipient;
    public $sourceImage;

    public function attributeLabels(): array
    {
        return [
            'sourceImage' => Yii::t('mr42', 'Source Image'),
            'recipient' => Yii::t('mr42', 'Email Address'),
        ];
    }

    public function convertImage(): bool
    {
        if (!$this->validate()) {
            Yii::$app->getSession()->setFlash('favicon-error', 'Uploaded file could not be converted. Make sure you upload a valid image.');
            FileHelper::unlink($this->sourceImage->tempName);
            return false;
        }

        FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));
        $cacheFile = Yii::getAlias('@assetsroot/temp/' . uniqid('favicon') . '.ico');

        $srcImg = new Imagick($this->sourceImage->tempName);
        $tmpSize = min($srcImg->getImageGeometry());
        $srcImg->setGravity(imagick::GRAVITY_CENTER);
        $srcImg->cropImage($tmpSize, $tmpSize, 0, 0);
        $srcImg->resizeImage(256, 256, Imagick::FILTER_LANCZOS, 1);

        $icon = new Imagick();
        $icon->setFormat("ico");
        foreach ($this->dimensions as $dim) {
            $clone = clone $srcImg;
            $clone->scaleImage($dim, 0);
            $icon->addImage($clone);
            $clone->destroy();
        }
        $icon->writeImages($cacheFile, true);
        $icon->destroy();

        $srcImg->destroy();
        FileHelper::unlink($this->sourceImage->tempName);

        if ($this->recipient) {
            Mailer::sendFileHtml($this->recipient, 'Your favicon from ' . Yii::$app->name, 'faviconRequester', ['file' => $cacheFile, 'name' => 'favicon.ico']);
        }

        Yii::$app->getSession()->setFlash('favicon-success', basename($cacheFile));
        return true;
    }

    public function rules(): array
    {
        return [
            ['recipient', 'email', 'checkDNS' => true, 'enableIDN' => true],
            ['sourceImage', 'required'],
            ['sourceImage', 'image', 'maxHeight' => 2500, 'maxWidth' => 2500, 'minHeight' => 256, 'minWidth' => 256],
        ];
    }
}
