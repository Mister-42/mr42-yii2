<?php
namespace app\test\unit;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase {
	private $vimeoVideo = '<div class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://player.vimeo.com/video/5780260?byline=0&amp;portrait=0&amp;title=0" allowfullscreen></iframe></div>';
	private $youtubeVideo = '<div class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://www.youtube-nocookie.com/embed/CdIenbh5Ju8?disablekb=1&amp;rel=0&amp;showinfo=0" allowfullscreen></iframe></div>';
	private $youtubePlaylist = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube-nocookie.com/embed/videoseries?disablekb=1&amp;list=PL6ugFMfi2vpN8uPV7OM2FMOlfF1nU2WJL&amp;showinfo=0" allowfullscreen></iframe></div>';

	public function testFormatter() {
		$this->assertEquals('<p>test</p>', \Yii::$app->formatter->cleanInput('test'));
		$this->assertEquals('<p>test</p>', \Yii::$app->formatter->cleanInput(' test'));
		$this->assertEquals('<p>test</p>', \Yii::$app->formatter->cleanInput('test '));
		$this->assertEquals('<p> Test</p>', \Yii::$app->formatter->cleanInput('<img src="test.png"> Test'));
		$this->assertEquals('<p><img src="test.png" class="img-fluid">Test</p>', \Yii::$app->formatter->cleanInput('<img src="test.png">Test', 'original', true));
		$this->assertEquals('<p><img src="test.png" class="img-fluid">Test</p>', \Yii::$app->formatter->cleanInput('<img src="test.png" width="1">Test', 'original', true));
		$this->assertEquals('<p><img src="test.png" class="img-fluid">Test</p>', \Yii::$app->formatter->cleanInput('<img src="test.png" height="1">Test', 'original', true));
		$this->assertEquals('<p><img src="test.png" class="img-fluid">Test</p>', \Yii::$app->formatter->cleanInput('<img src="test.png" width="1" height="1">Test', 'original', true));
		$this->assertEquals('<p><img src="test.png" class="img-fluid">Test</p>', \Yii::$app->formatter->cleanInput('<img src="test.png" height="1" width="1">Test', 'original', true));
		$this->assertEquals('<p><img src="test.png" class="testClass img-fluid">Test</p>', \Yii::$app->formatter->cleanInput('<img src="test.png" class="testClass">Test', 'original', true));
	}

	public function testImage() {
		$this->assertEquals('<p><img src="//example.com/image.png" alt="aLt" title="tiTle" class="img-fluid"></p>', \Yii::$app->formatter->cleanInput('![aLt](//example.com/image.png "tiTle")'));
	}

	public function testVideo() {
		$this->assertEquals($this->vimeoVideo, \Yii::$app->formatter->cleanInput('vimeo:5780260:4by3'));
		$this->assertEquals($this->youtubeVideo, \Yii::$app->formatter->cleanInput('youtube:CdIenbh5Ju8:4by3'));
		$this->assertEquals($this->youtubePlaylist, \Yii::$app->formatter->cleanInput('youtube:PL6ugFMfi2vpN8uPV7OM2FMOlfF1nU2WJL:16by9'));
	}
}
