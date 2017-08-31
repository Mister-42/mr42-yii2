<?php
namespace app\models\feed;
use XMLWriter;

class Sitemap {
	public function lineItem(XMLWriter $doc, string $url, int $age = null, float $priority = null): XMLWriter {
		$priority = $priority ?? self::getPriority($age);
		$doc->startElement('url');
		$doc->writeElement('loc', $url);
		if ($age) $doc->writeElement('lastmod', date(DATE_W3C, $age));
		$doc->writeElement('changefreq', self::getChangefreq($priority));
		$doc->writeElement('priority', round($priority, 2));
		$doc->endElement();
		return $doc;
	}

	private function getChangefreq(float $priority): string {
		if ($priority >= 0.9)		return 'daily';
		elseif ($priority >= 0.8)	return 'weekly';
		elseif ($priority >= 0.7)	return 'monthly';
		elseif ($priority >= 0.6)	return 'yearly';
		return 'never';
	}

	private function getPriority(int $age): float {
		if ($age > strtotime("-1 week"))		return 0.9;
		elseif ($age > strtotime("-1 month"))	return 0.8;
		elseif ($age > strtotime("-1 year"))	return 0.7;
		return 0.5;
	}
}
