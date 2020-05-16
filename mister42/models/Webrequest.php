<?php

namespace mister42\models;

use mister42\Secrets;
use Yii;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Request;
use yii\httpclient\Response;

class Webrequest
{
    public static function getDiscogsApi(string $content): Response
    {
        return self::getUrl('https://api.discogs.com/', $content)->send();
    }

    public static function getLastfmApi(string $method, array $data): Response
    {
        $secrets = (new Secrets())->getValues();
        return self::getUrl('https://ws.audioscrobbler.com/2.0/', '', array_merge($data, [
            'api_key' => $secrets['last.fm']['API'],
            'method' => $method,
        ]))->send();
    }

    public static function getUrl(string $base, string $url, array $data = []): Request
    {
        $client = ($base === 'file') ? new Client(['transport' => CurlTransport::class]) : new Client(['baseUrl' => $base]);
        return $client->createRequest()
            ->addHeaders(['user-agent' => Yii::$app->name . ' (+' . Yii::$app->params['shortDomain'] . ')'])
            ->setData($data)
            ->setUrl($url);
    }

    public static function getYoutubeApi(string $id, string $content): Response
    {
        $secrets = (new Secrets())->getValues();
        return self::getUrl('https://www.googleapis.com/youtube/v3', $content, [
            'id' => $id,
            'key' => $secrets['google']['API'],
            'part' => $content === 'videos' ? 'snippet,status' : 'contentDetails,status',
        ])->send();
    }

    public static function saveUrl(string $url, string $file, array $data = []): Response
    {
        $fh = fopen($file, 'w');
        $response = self::getUrl('file', $url, $data)
            ->setOutputFile($fh)
            ->send();
        fclose($fh);
        return $response;
    }
}
