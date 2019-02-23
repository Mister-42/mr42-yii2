<?php
namespace app\test\unit;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase {
	public function testFeedController() {
		$this->assertContains('</item>', \Yii::$app->runAction('feed/rss'));
		$this->assertContains('</urlset>', \Yii::$app->runAction('feed/sitemap'));
		$this->assertContains('</urlset>', \Yii::$app->runAction('feed/sitemap-articles'));
		$this->assertContains('</urlset>', \Yii::$app->runAction('feed/sitemap-lyrics'));
	}

	public function testLyricsController() {
		$this->assertContains('Nórdika', \Yii::$app->runAction('music/lyrics'));
		$_GET = ['artist' => 'Nordika'];
		$this->assertContains('Ecstasy', \Yii::$app->runAction('music/lyrics'));
		$_GET = ['artist' => 'Nordika', 'year' => 2017, 'album' => 'Ecstasy'];
		$this->assertContains('In oblivion…', \Yii::$app->runAction('music/lyrics'));
	}

	public function testSiteController() {
		$this->assertContains('&copy; 2014', \Yii::$app->runAction('site/index'));
		$this->assertInstanceOf(\yii\web\Response::class, \Yii::$app->runAction('site/faviconico'));
		$this->assertContains('maintenance', \Yii::$app->runAction('site/offline'));
		$this->assertContains('feed/rss', \Yii::$app->runAction('site/browserconfigxml'));
		$this->assertContains('sitemap.xml', \Yii::$app->runAction('site/robotstxt'));
		$this->assertObjectHasAttribute('data', \Yii::$app->runAction('site/webmanifest'));
	}
}
