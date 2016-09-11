<?php
namespace app\models;
use DateTime;
use Yii;
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
   
		$format = [];
		if($interval->y !== 0)
			$format[] = Yii::t('app', '{delta, plural, =1{1 year} other{# years}}', ['delta' => $interval->y]);
		if($interval->m !== 0)
			$format[] = Yii::t('app', '{delta, plural, =1{1 month} other{# months}}', ['delta' => $interval->m]);
		if($interval->d !== 0)
			$format[] = Yii::t('app', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $interval->d]);
		if($interval->h !== 0)
			$format[] = Yii::t('app', '{delta, plural, =1{1 hour} other{# hours}}', ['delta' => $interval->h]);
		if($interval->i !== 0)
			$format[] = Yii::t('app', '{delta, plural, =1{1 minute} other{# minutes}}', ['delta' => $interval->i]);
		if($interval->s !== 0) {
			if(!count($format)) {
				return Yii::t('app', 'just now');
			}
			$format[] = Yii::t('app', '{delta, plural, =1{1 second} other{# seconds}}', ['delta' => $interval->s]);
		}

		$format = (count($format) > 1) ? array_shift($format).' and '.array_shift($format) : array_pop($format);
		return $interval->format($format) . ' ago';
	}
}
