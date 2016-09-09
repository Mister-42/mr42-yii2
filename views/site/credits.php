<?php
use yii\helpers\Html;

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
		['Less.php', 'http://lessphp.typesettercms.com', 'Josh Schmidt'],
		['Bluefish', 'http://bluefish.openoffice.nl/'],
		['GitHub', 'https://github.com/'],
		['ScienceDaily', 'https://www.sciencedaily.com/'],
		['Last.fm', 'http://www.last.fm/'],
		['ImageMagick', 'http://imagemagick.org/'],
		['Swift Mailer', 'http://swiftmailer.org/'],
		['HTML Purifier', 'http://htmlpurifier.org/'],
		['PHP Markdown', 'https://michelf.ca/projects/php-markdown/', 'Michel Fortin'],
		['yii2-user', 'https://yii2-user.dektrium.com/', 'Dmitry Erofeev'],
		['DS Etienne Font', 'http://www.1001fonts.com/ds-etienne-font.html', 'Dieter Steffmann'],
		['clipboard.js', 'https://clipboardjs.com/', 'Zeno Rocha'],
		['pbkdf2.js', 'http://anandam.com/pbkdf2', 'Parvez Anandam'],
		['sha1.js', 'http://pajhome.org.uk/crypt/md5', 'Paul Johnston'],
		['mPDF', 'http://www.mpdf1.com/'],
	];

	$x=0; $y=0;
	foreach ($list as $item) {
		$x++; $y++;
		if ($x == 1)
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
