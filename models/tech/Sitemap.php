<?php
namespace app\models\tech;
use DOMDocument;
use DOMElement;

class Sitemap
{
	public static function ageData(DOMDocument $dom, DOMElement $child, $url, $age, $prio = null) {
		if ($prio === null)
			return self::prioData($dom, $child, $url, self::age2Prio($age), $age);

		return self::prioData($dom, $child, $url, $prio, $age);
	}

	public static function prioData(DOMDocument $dom, DOMElement $child, $url, $priority, $age = '') {
		$content = $dom->createElement('url');
		$content->appendChild($dom->createElement('loc', $url));
		if ($age) $content->appendChild($dom->createElement('lastmod', date(DATE_W3C, $age)));
		$content->appendChild($dom->createElement('changefreq', self::priority2Changefreq($priority)));
		$content->appendChild($dom->createElement('priority', number_format($priority, 2)));
		$child->appendChild($content);
	}

	private static function age2Prio($age) {
		if( $age > strtotime("-1 week") )		return 0.9;
		elseif( $age > strtotime("-1 month") )	return 0.8;
		elseif( $age > strtotime("-1 year") )	return 0.7;
		else												return 0.5;
	}

	private static function priority2Changefreq($priority) {
		if( $priority >= 0.9 )		return 'daily';
		elseif( $priority >= 0.8 )	return 'weekly';
		elseif( $priority >= 0.7 )	return 'monthly';
		elseif( $priority >= 0.6 )	return 'yearly';
		else								return 'never';
	}
}
