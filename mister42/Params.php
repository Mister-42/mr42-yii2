<?php
class Params {
	public function getValues() {
		return [
			'description' => 'Sharing beautiful knowledge of the world.',
			'languages' => [
				'en' => 'English',
				'de' => 'Deutsch',
				'ru' => 'Русский',
			],
			'secrets' => (new Secrets())->getValues(),
			'shortDomain' => 'https://www.mr42.me/',
			'themeColor' => '#003865',
		];
	}
}
