<?php
namespace app\models\feed;
use XMLWriter;

class Sitemap {
	public static function lineItem(XMLWriter $doc, string $url, int $age = null, float $priority = null): XMLWriter {
		$priority = $priority ?? self::getPriority($age);
		$doc->startElement('url');
		$doc->writeElement('loc', $url);
		if ($age) :
			$doc->writeElement('lastmod', date(DATE_W3C, $age));
		endif;
		$doc->writeElement('changefreq', self::getChangefreq($priority));
		$doc->writeElement('priority', round($priority, 2));
		$doc->endElement();
		return $doc;
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
		if ($age > strtotime("-1 week")) :
			$prio = 0.9;
		elseif ($age > strtotime("-1 month")) :
			$prio = 0.8;
		elseif ($age > strtotime("-1 year")) :
			$prio = 0.7;
		endif;
		return $prio ?? 0.5;
	}
}
