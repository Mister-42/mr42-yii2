<?php
namespace app\test\unit;
use app\models\Webrequest;
use PHPUnit\Framework\TestCase;

class WebrequestTest extends TestCase {
	public function testLastfmApi() {
		$this->assertTrue(Webrequest::getLastfmApi('user.getweeklyartistchart', ['user' => 'rj', 'limit' => 5])->isOK);
	}

	public function testYoutubeApi() {
		$this->assertTrue(Webrequest::getYoutubeApi('tsI3RsKTqNU', 'videos')->isOK);
		$this->assertTrue(Webrequest::getYoutubeApi('PL6ugFMfi2vpN8uPV7OM2FMOlfF1nU2WJL', 'playlists')->isOK);
	}
}
