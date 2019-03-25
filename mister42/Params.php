<?php
namespace mister42;

class Params {
	public function getValues(): array {
		return [
			'description' => 'Sharing beautiful knowledge of the world.',
			'languages' => [
				'en' => 'English',
				'de' => 'Deutsch',
				'ru' => 'Русский',
			],
			'longDomain' => 'https://www.mister42.me',
			'shortDomain' => 'https://mr42.me',
			'themeColor' => '#003865',
		];
	}
}
