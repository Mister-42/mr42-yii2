<?php
namespace app\models\feed;
use XMLWriter;

class Sitemap {
	public function ageData(XMLWriter $dom, $url, $age, $prio = null) {
		if (!$prio)
			return self::prioData($dom, $url, self::age2Prio($age), $age);
		return self::prioData($dom, $url, $prio, $age);
	}

	public function prioData(XMLWriter $doc, $url, $priority, $age = null) {
		$doc->startElement('url');
		$doc->writeElement('loc', $url);
		if ($age) $doc->writeElement('lastmod', date(DATE_W3C, $age));
		$doc->writeElement('changefreq', self::prio2Changefreq($priority));
		$doc->writeElement('priority', number_format($priority, 2));
		$doc->endElement();
	}

	private function age2Prio($age) {
		if( $age > strtotime("-1 week") )		return 0.9;
		elseif( $age > strtotime("-1 month") )	return 0.8;
		elseif( $age > strtotime("-1 year") )	return 0.7;
		return 0.5;
	}

	private function prio2Changefreq($priority) {
		if( $priority >= 0.9 )		return 'daily';
		elseif( $priority >= 0.8 )	return 'weekly';
		elseif( $priority >= 0.7 )	return 'monthly';
		elseif( $priority >= 0.6 )	return 'yearly';
		return 'never';
	}
}
