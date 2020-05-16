<?php

namespace mister42;

class Params
{
    public function getValues(): array
    {
        return [
            'languages' => [
                'en' => ['full' => 'English', 'short' => 'EN'],
                'de' => ['full' => 'Deutsch', 'short' => 'DE'],
                'ru' => ['full' => 'Русский', 'short' => 'RU'],
            ],
            'longDomain' => 'https://www.mister42.me',
            'shortDomain' => 'https://mr42.me',
            'themeColor' => '#003865',
        ];
    }
}
