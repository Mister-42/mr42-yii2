<?php
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('mr42', 'My Pi');
$this->params['breadcrumbs'][] = $this->title;

$tabs = [
	'day' => ['short' => Yii::t('mr42', 'Day'), 'long' => Yii::t('mr42', 'Last Day')],
	'week' => ['short' => Yii::t('mr42', 'Week'), 'long' => Yii::t('mr42', 'Last Week')],
	'month' => ['short' => Yii::t('mr42', 'Month'), 'long' => Yii::t('mr42', 'Last Month')],
];
$datatype = [
	'tempload' => Yii::t('mr42', 'Temperature & Load'),
	'network' => Yii::t('mr42', 'Network Traffic'),
	'memory' => Yii::t('mr42', 'Memory Usage'),
	'diskspace' => Yii::t('mr42', 'Disk Space Usage'),
];
$hosts = ['dnspi', 'musicpi'];

$this->registerJs(Yii::$app->formatter->jspack('jquery.unveil.js'), View::POS_END);
$this->registerJs('$(\'a[data-toggle="tab"]\').on(\'shown.bs.tab\', function (e) {$(window).trigger("lookup")})', View::POS_END);
$this->registerJs('$("img").unveil();', View::POS_READY);

echo Html::tag('h1', $this->title);

foreach ($tabs as $tab => $tabdesc)
	$tabdata[] = Html::a(ucfirst($tabdesc['short']), "#{$tab}", ['aria-controls' => $tab, 'aria-selected' => ($tab === array_key_first($tabs)) ? 'true' : 'false', 'class' => ($tab === array_key_first($tabs)) ? 'nav-link active' : 'nav-link', 'data-toggle' => 'tab', 'id' => "{$tab}-tab", 'role' => 'tab']);
echo Html::ul($tabdata, ['class' => 'nav nav-tabs', 'id' => 'nav-tabs', 'encode' => false, 'itemOptions' => ['class' => 'nav-item'], 'role' => 'tablist']);

echo Html::beginTag('div', ['class' => 'tab-content']);
	foreach ($tabs as $tab => $tabdesc) :
		echo Html::beginTag('div', ['aria-labelledby' => "{$tab}-tab", 'class' => ($tab === array_key_first($tabs)) ? 'tab-pane fade show active' : 'tab-pane fade', 'id' => $tab, 'role' => 'tabpanel']);
			echo Html::beginTag('div', ['class' => 'row']);
				echo Html::tag('div', Html::tag('h3', $tabdesc['long'], ['class' => 'text-center mt-2']), ['class' => 'col-12']);
				foreach ($datatype as $dt => $dtdesc) :
					echo Html::tag('h4', $dtdesc, ['class' => 'w-100 text-center mt-2 mb-0']);
					foreach ($hosts as $host) :
						echo Html::beginTag('div', ['class' => 'col-md-6 h-100']);
							echo Html::tag('h5', $host, ['class' => 'text-center my-0']);
							echo ($tab === array_key_first($tabs))
								? Html::img("@assets/pi/{$tab}-{$host}-{$dt}.png", ['alt' => yii::t('mr42', '{desc} of {host}', ['desc' => $dtdesc, 'host' => $host])." ({$tabdesc['long']})", 'class' => 'img-fluid mb-2'])
								: Html::img("@assets/images/loading.png", ['alt' => Yii::t('mr42', '{desc} of {host}', ['desc' => $dtdesc, 'host' => $host])." ({$tabdesc['long']})", 'class' => 'img-fluid mb-2', 'data-src' => Yii::getAlias("@assets/pi/{$tab}-{$host}-{$dt}.png")]);
						echo Html::endTag('div');
					endforeach;
				endforeach;
			echo Html::endTag('div');
		echo Html::endTag('div');
	endforeach;
echo Html::endTag('div');
