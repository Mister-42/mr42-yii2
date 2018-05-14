<?php
namespace app\test\unit;
use app\models\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase {
	private $vimeoVideo = '<div class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://player.vimeo.com/video/5780260?byline=0&amp;portrait=0&amp;title=0" allowfullscreen></iframe></div>';
	private $youtubeVideo = '<div class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://www.youtube-nocookie.com/embed/CdIenbh5Ju8?disablekb=1&amp;rel=0&amp;showinfo=0" allowfullscreen></iframe></div>';
	private $youtubePlaylist = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube-nocookie.com/embed/videoseries?disablekb=1&amp;list=PL6ugFMfi2vpN8uPV7OM2FMOlfF1nU2WJL&amp;showinfo=0" allowfullscreen></iframe></div>';

	public function testVideo() {
		$this->assertEquals('<p>test</p>', Formatter::cleanInput('test'));
		$this->assertEquals($this->vimeoVideo, Formatter::cleanInput('vimeo:5780260:4by3'));
		$this->assertEquals($this->youtubeVideo, Formatter::cleanInput('youtube:CdIenbh5Ju8:4by3'));
		$this->assertEquals($this->youtubePlaylist, Formatter::cleanInput('youtube:PL6ugFMfi2vpN8uPV7OM2FMOlfF1nU2WJL:16by9'));
	}
}
