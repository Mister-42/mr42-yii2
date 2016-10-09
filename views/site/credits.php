<?php
use yii\bootstrap\Html;

$this->title = 'Credits';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-credits">
	<?= Html::tag('h1', Html::encode($this->title)) ?>

	<?php $list = [
		['Yii Framework', 'http://www.yiiframework.com/'],
		['Bootstrap', 'http://getbootstrap.com/'],
		['jQuery', 'http://jquery.com/'],
		['jQuery UI', 'http://jqueryui.com/'],
		['Sass', 'http://sass-lang.com/'],
		['Bluefish', 'http://bluefish.openoffice.nl/'],
		['GitHub', 'https://github.com/'],
		['ScienceDaily', 'https://www.sciencedaily.com/'],
		['Last.fm', 'http://www.last.fm/'],
		['ImageMagick', 'http://imagemagick.org/'],
		['Swift Mailer', 'http://swiftmailer.org/'],
		['HTML Purifier', 'http://htmlpurifier.org/'],
		['PHP Markdown', 'https://michelf.ca/projects/php-markdown/', 'Michel Fortin'],
		['highlight.js', 'https://highlightjs.org/', 'Ivan Sagalaev'],
		['yii2-user', 'https://yii2-user.dektrium.com/', 'Dmitry Erofeev'],
		['clipboard.js', 'https://clipboardjs.com/', 'Zeno Rocha'],
		['pbkdf2.js', 'http://anandam.com/pbkdf2', 'Parvez Anandam'],
		['sha1.js', 'http://pajhome.org.uk/crypt/md5', 'Paul Johnston'],
		['mPDF', 'http://www.mpdf1.com/'],
	];

	$x = $y = 0;
	foreach ($list as $item) {
		$y++;
		if ($x++ === 0)
			echo '<div class="col-sm-6 text-center text-nowrap">';

		echo Html::a($item[0], $item[1]);
		if (!empty($item[2]))
			echo ' - ' . $item[2];
		echo '<br />';

		if ($x == ceil(count($list)/2) || $y == count($list)) {
			echo '</div>';
			$x=0;
		}
	} ?>
</div>
