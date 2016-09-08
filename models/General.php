<?php
namespace app\models;
use DateTime;
use yii\helpers\HtmlPurifier;
use yii\helpers\Markdown;

class General
{
	public static function cleanInput($data, $markdown = 'original', $allowHtml = false)
	{
		$data = ($allowHtml) ? HtmlPurifier::process($data) : HtmlPurifier::process($data, ['HTML.Allowed' => '']);
		if ($markdown)
			$data = Markdown::process($data, $markdown);
		if ($allowHtml)
			$data = preg_replace('#@yt\[([a-z0-9.-]+)\]#i', '<div class="video"><iframe src="https://www.youtube.com/embed/$1?wmode=opaque" frameborder="0" allowfullscreen></iframe></div>', $data);
		return trim($data);
	}

	public static function timeAgo($date)
	{
		$start = new DateTime('@'.$date);
		$end = new DateTime();

		$interval = $end->diff($start);
		$doPlural = function($nb, $str){ return $nb > 1 ? $str.'s' : $str; };
   
		$format = [];
		if($interval->y !== 0)
			$format[] = "%y ".$doPlural($interval->y, "year");
		if($interval->m !== 0)
			$format[] = "%m ".$doPlural($interval->m, "month");
		if($interval->d !== 0)
			$format[] = "%d ".$doPlural($interval->d, "day");
		if($interval->h !== 0)
			$format[] = "%h ".$doPlural($interval->h, "hour");
		if($interval->i !== 0)
			$format[] = "%i ".$doPlural($interval->i, "minute");
		if($interval->s !== 0) {
			if(!count($format)) {
				return "less than a minute ago";
			}
			$format[] = "%s ".$doPlural($interval->s, "second");
		}

		$format = (count($format) > 1) ? array_shift($format)." and ".array_shift($format) : array_pop($format);
		return $interval->format($format) . ' ago';
	}
}
