<?php

namespace mister42\test;

class ControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testMusicController(): void
    {
        $this->assertStringContainsString('Nórdika', \Yii::$app->runAction('music/lyrics'));
        $_GET = ['artist' => 'Nordika'];
        $this->assertStringContainsString('Ecstasy', \Yii::$app->runAction('music/lyrics'));
        $_GET = ['artist' => 'Nordika', 'year' => 2017, 'album' => 'Ecstasy'];
        $this->assertStringContainsString('In oblivion…', \Yii::$app->runAction('music/lyrics'));
    }

    public function testSiteController(): void
    {
        $this->assertStringContainsString('&copy; 2014', \Yii::$app->runAction('site/index'));
        $this->assertInstanceOf(\yii\web\Response::class, \Yii::$app->runAction('site/faviconico'));
        $this->assertStringContainsString('maintenance', \Yii::$app->runAction('site/offline'));
        $this->assertStringContainsString('feed/rss', \Yii::$app->runAction('site/browserconfigxml'));
        $this->assertStringContainsString('sitemap.xml', \Yii::$app->runAction('site/robotstxt'));
        $this->assertObjectHasAttribute('data', \Yii::$app->runAction('site/webmanifest'));
    }
}
