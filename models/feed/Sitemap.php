<?php
namespace app\models\feed;
use XMLElement;
use XMLWriter;

class Sitemap {
	public function ageData(XMLWriter $dom, string $url, int $age, float $prio = null) {
		return ($prio)
			? self::prioData($dom, $url, $prio, $age)
			: self::prioData($dom, $url, self::age2Prio($age), $age);
	}

	public function prioData(XMLWriter $doc, string $url, float $prio, int $age = null) {
		$doc->startElement('url');
		$doc->writeElement('loc', $url);
		if ($age) $doc->writeElement('lastmod', date(DATE_W3C, $age));
		$doc->writeElement('changefreq', self::prio2Changefreq($prio));
		$doc->writeElement('priority', round($prio, 2));
		$doc->endElement();
	}

	private function age2Prio(int $age): float {
		if ($age > strtotime("-1 week"))		return 0.9;
		elseif ($age > strtotime("-1 month"))	return 0.8;
		elseif ($age > strtotime("-1 year"))	return 0.7;
		return 0.5;
	}

	private function prio2Changefreq(float $prio): string {
		if ($prio >= 0.9)		return 'daily';
		elseif ($prio >= 0.8)	return 'weekly';
		elseif ($prio >= 0.7)	return 'monthly';
		elseif ($prio >= 0.6)	return 'yearly';
		return 'never';
	}
}
