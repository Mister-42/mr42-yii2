<?php

namespace app\models;

class Console extends \yii\helpers\Console {
	public static function startProgress($done, $total, $prefix = '', $width = null): void {
		parent::startProgress($done, $total, self::ansiFormat($prefix, [self::BOLD, self::FG_BLUE]), $width);
	}

	public static function endProgress($remove = false, $keepPrefix = true): void {
		parent::endProgress($remove, $keepPrefix);
		self::write('Done', [self::BOLD, self::FG_GREEN]);
		self::newLine();
	}

	public static function write(string $msg, array $format, int $tabs = 1): void {
		$output = self::ansiFormat($msg, $format);
		self::stdout($output);
		for ($x = 0; $x < ($tabs - intdiv(self::ansiStrlen($output), 8)); $x++) {
			self::stdout("\t");
		}
	}

	public static function writeError(string $msg, array $format): void {
		$output = self::ansiFormat($msg, $format);
		self::error($output);
	}

	public static function newLine(): void {
		self::stdout(PHP_EOL);
	}
}
