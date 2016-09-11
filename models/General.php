<?php
namespace app\models;
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
}
