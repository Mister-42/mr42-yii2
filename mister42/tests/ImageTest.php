<?php

namespace app\tests;

use app\models\Image;

class ImageTest extends \PHPUnit\Framework\TestCase
{
    public function testAverageColor(): void
    {
        $imgTesla = file_get_contents(\Yii::getAlias('@assetsroot/images/article/014_Elon_Musks_Tesla_Roadster.jpg'));
        $this->assertSame('#201C23', Image::getAverageImageColor($imgTesla));
        $imgLogo = file_get_contents(\Yii::getAlias('@assetsroot/images/mr42.png'));
        $this->assertSame('#00192A', Image::getAverageImageColor($imgLogo));
    }

    public function testResize(): void
    {
        $imgTesla = file_get_contents(\Yii::getAlias('@assetsroot/images/article/014_Elon_Musks_Tesla_Roadster.jpg'));
        $this->assertSame([250, 141, IMG_JPG, 'width="250" height="141"', 'bits' => 8, 'channels' => 3, 'mime' => 'image/jpeg'], getimagesizefromstring(Image::resize($imgTesla, 250)));
    }
}
