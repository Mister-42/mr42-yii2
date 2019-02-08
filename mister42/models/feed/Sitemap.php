<?php
namespace app\models\feed;
use XMLWriter;
use Yii;
use yii\helpers\{ArrayHelper, Url};

class Sitemap {
	public static function beginDoc(): XmlWriter {
		$doc = new XMLWriter();
		$doc->openMemory();
		$doc->setIndent(YII_DEBUG && php_sapi_name() !== 'cli' && (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin));

		$doc->startDocument('1.0', 'UTF-8');
		$doc->startElement('urlset');
		$doc->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$doc->writeAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
		$doc->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$doc->writeAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
		return $doc;
	}

	public static function lineItem(XMLWriter $doc, array $url, array $options = []) {
		$age = (int) ArrayHelper::remove($options, 'age', 0);
		$priority = ArrayHelper::remove($options, 'priority') ?? self::getPriority($age);

		$doc->startElement('url');
		$doc->writeElement('loc', Url::to($url, true));
		if ($age) :
			$doc->writeElement('lastmod', date(DATE_W3C, $age));
		endif;
		$doc->writeElement('changefreq', self::getChangefreq($priority));
		$doc->writeElement('priority', round($priority, 2));
		if (ArrayHelper::remove($options, 'locale')) :
			self::addLanguageLines($doc, $url);
		endif;
		$doc->endElement();
	}

	public static function endDoc(XMLWriter $doc): string {
		$doc->endElement();
		$doc->endDocument();
		return $doc->outputMemory();
	}

	private static function addLanguageLines(XMLWriter $doc, array $url) {
		$languages = array_keys(Yii::$app->params['languages']);
		foreach ($languages as $lng) :
			ArrayHelper::setValue($url, 'language', $lng);
			$doc->startElement('xhtml:link');
			$doc->writeAttribute('href', Url::to($url, true));
			$doc->writeAttribute('hreflang', $lng);
			$doc->writeAttribute('rel', 'alternate');
			$doc->endElement();
		endforeach;
	}

	private static function getChangefreq(float $priority): string {
		if ($priority >= 0.9) :
			$freq = 'daily';
		elseif ($priority >= 0.8) :
			$freq = 'weekly';
		elseif ($priority >= 0.7) :
			$freq = 'monthly';
		elseif ($priority >= 0.6) :
			$freq = 'yearly';
		endif;
		return $freq ?? 'never';
	}

	private static function getPriority(int $age): float {
		if ($age > strtotime('-1 week')) :
			$prio = 0.9;
		elseif ($age > strtotime('-1 month')) :
			$prio = 0.8;
		elseif ($age > strtotime('-1 year')) :
			$prio = 0.7;
		endif;
		return $prio ?? 0.5;
	}
}
