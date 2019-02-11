<?php
namespace app\models;
use Yii;

class Console extends \yii\helpers\Console {
	public static function write(string $msg, array $format, int $tabs = 1) {
		$output = self::ansiFormat($msg, $format);
		self::stdout($output);
		for ($x = 0; $x < ($tabs - intdiv(self::ansiStrlen($output), 8)); $x++)
			self::stdout("\t");
	}

	public static function writeError(string $msg, array $format) {
		$output = self::ansiFormat($msg, $format);
		self::stderr($output);
		self::newLine();
	}

	public static function newLine() {
		self::stdout(PHP_EOL);
	}
}
