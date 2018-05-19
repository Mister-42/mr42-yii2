<?php
namespace app\test\unit;
use app\models\Video;
use PHPUnit\Framework\TestCase;

class VideoTest extends TestCase {
	private $vimeoVideo = '<div class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://player.vimeo.com/video/5780260?byline=0&amp;portrait=0&amp;title=0" allowfullscreen></iframe></div>';
	private $youtubeVideo = '<div class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://www.youtube-nocookie.com/embed/CdIenbh5Ju8?disablekb=1&amp;rel=0&amp;showinfo=0" allowfullscreen></iframe></div>';
	private $youtubePlaylist = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube-nocookie.com/embed/videoseries?disablekb=1&amp;list=PL6ugFMfi2vpN8uPV7OM2FMOlfF1nU2WJL&amp;showinfo=0" allowfullscreen></iframe></div>';

	public function testEmbed() {
		$this->assertEquals($this->vimeoVideo, Video::getEmbed('vimeo', '5780260', '4by3'));
		$this->assertEquals($this->youtubeVideo, Video::getEmbed('youtube', 'CdIenbh5Ju8', '4by3'));
		$this->assertEquals($this->youtubePlaylist, Video::getEmbed('youtube', 'PL6ugFMfi2vpN8uPV7OM2FMOlfF1nU2WJL', '16by9', true));
	}

	public function testUrl() {
		$this->assertEquals('https://vimeo.com/5780260', Video::getUrl('vimeo', '5780260'));
		$this->assertEquals('https://vimeo.com/album/1539680', Video::getUrl('vimeo', '1539680', true));
		$this->assertEquals('https://youtu.be/CdIenbh5Ju8', Video::getUrl('youtube', 'CdIenbh5Ju8'));
		$this->assertEquals('https://www.youtube.com/playlist?list=PL6ugFMfi2vpN8uPV7OM2FMOlfF1nU2WJL', Video::getUrl('youtube', 'PL6ugFMfi2vpN8uPV7OM2FMOlfF1nU2WJL', true));
	}
}
