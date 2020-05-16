<?php

namespace mr42\test;

class ControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testArticlesController(): void
    {
        $this->assertInstanceOf(\yii\web\Response::class, \Yii::$app->runAction('articles/pdf', ['id' => 4, 'title' => 'Markdown Syntax']));
    }

    public function testFeedController(): void
    {
        $this->assertStringContainsString('</item>', \Yii::$app->runAction('feed/rss'));
        $this->assertStringContainsString('</urlset>', \Yii::$app->runAction('feed/sitemap'));
        $this->assertStringContainsString('</urlset>', \Yii::$app->runAction('feed/sitemap-articles'));
        $this->assertStringContainsString('</urlset>', \Yii::$app->runAction('feed/sitemap-lyrics'));
    }

    public function testMusicController(): void
    {
        $_GET = ['artist' => 'Nordika', 'year' => 2017, 'album' => 'Ecstasy'];
        $this->assertInstanceOf(\yii\web\Response::class, \Yii::$app->runAction('music/albumpdf'));
    }
}
