<?php

namespace mr42\models;

use XMLWriter;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Sitemap
{
    public static function beginDoc(): XmlWriter
    {
        $doc = new XMLWriter();
        $doc->openMemory();
        $doc->setIndent(YII_DEBUG);

        $doc->startDocument('1.0', 'UTF-8');
        $doc->startElement('urlset');
        $doc->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $doc->writeAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
        $doc->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $doc->writeAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        return $doc;
    }

    public static function endDoc(XMLWriter $doc): string
    {
        $doc->endElement();
        $doc->endDocument();
        return $doc->outputMemory();
    }

    public static function lineItem(XMLWriter $doc, $url, array $options = []): void
    {
        $age = (int) ArrayHelper::remove($options, 'age', 0);
        $priority = ArrayHelper::remove($options, 'priority') ?? self::getPriority($age);
        $url = is_array($url) ? Url::to($url) : $url;

        $doc->startElement('url');
        $doc->writeElement('loc', $url);
        if ($age) {
            $doc->writeElement('lastmod', date(DATE_W3C, $age));
        }
        $doc->writeElement('changefreq', self::getChangefreq($priority));
        $doc->writeElement('priority', round($priority, 2));
        if (ArrayHelper::remove($options, 'locale')) {
            self::addLanguageLines($doc, $url);
        }
        $doc->endElement();
    }

    private static function addLanguageLines(XMLWriter $doc, string $url): void
    {
        $languages = array_keys(Yii::$app->params['languages']);
        $path = parse_url($url, PHP_URL_PATH);
        foreach ($languages as $lng) {
            $doc->startElement('xhtml:link');
            $lngAlias = '@site' . strtoupper($lng);
            $doc->writeAttribute('href', Url::to($lngAlias) . $path);
            $doc->writeAttribute('hreflang', $lng);
            $doc->writeAttribute('rel', 'alternate');
            $doc->endElement();
        }
    }

    private static function getChangefreq(float $priority): string
    {
        if ($priority >= 0.9) {
            $freq = 'daily';
        } elseif ($priority >= 0.8) {
            $freq = 'weekly';
        } elseif ($priority >= 0.7) {
            $freq = 'monthly';
        } elseif ($priority >= 0.6) {
            $freq = 'yearly';
        }
        return $freq ?? 'never';
    }

    private static function getPriority(int $age): float
    {
        if ($age > strtotime('-1 week')) {
            $prio = 0.9;
        } elseif ($age > strtotime('-1 month')) {
            $prio = 0.8;
        } elseif ($age > strtotime('-1 year')) {
            $prio = 0.7;
        }
        return $prio ?? 0.5;
    }
}
