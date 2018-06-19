<?php
namespace app\models;

class Image {
	public static function resize(string $image, int $size): string {
		$process = proc_open("convert -resize {$size} -strip -quality 85% -interlace Plane - jpg:-", [['pipe', 'r'], ['pipe', 'w']], $pipes);
		if (is_resource($process)) :
			fwrite($pipes[0], $image);
			fclose($pipes[0]);

			$image = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			proc_close($process);
		endif;

		return $image;
	}
}
