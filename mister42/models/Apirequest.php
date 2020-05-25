<?php

namespace mister42\models;

use mister42\Secrets;
use thoulah\httpclient\Client;
use yii\httpclient\Response;

class Apirequest
{
    public static function getDiscogs(string $content): Response
    {
        $client = new Client('https://api.discogs.com/');
        return $client->getUrl($content);
    }

    public static function getLastfm(string $method, array $data): Response
    {
        $secrets = (new Secrets())->getValues();
        $client = new Client('https://ws.audioscrobbler.com/2.0/');
        return $client->getUrl('', array_merge($data, [
            'api_key' => $secrets['last.fm']['API'],
            'method' => $method,
        ]));
    }

    public static function getYoutube(string $id, string $content): Response
    {
        $secrets = (new Secrets())->getValues();
        $client = new Client('https://www.googleapis.com/youtube/v3');
        return $client->getUrl($content, [
            'id' => $id,
            'key' => $secrets['google']['API'],
            'part' => $content === 'videos' ? 'snippet,status' : 'contentDetails,status',
        ]);
    }
}
