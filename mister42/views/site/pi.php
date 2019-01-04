<?php
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('mr42', 'My Pi');
$this->params['breadcrumbs'][] = $this->title;
$tabs = [	'tempload' => ['desc' => 'Temperature & Load', 'shortdesc' => yii::t('mr42', 'Temperature/Load')],
			'diskspace' => ['desc' => 'Disk Space Usage', 'shortdesc' => yii::t('mr42', 'Disk')],
			'memory' => ['desc' => 'Memory Usage', 'shortdesc' => yii::t('mr42', 'Memory')],
			'network' => ['desc' => 'Network Usage', 'shortdesc' => yii::t('mr42', 'Network')],
	];
$hosts = ['musicpi'];
$recurrence = ['day', 'week'];

$this->registerJs(Yii::$app->formatter->jspack('jquery.unveil.js'), View::POS_END);
$this->registerJs('$(\'a[data-toggle="tab"]\').on(\'shown.bs.tab\', function (e) {$(window).trigger("lookup")})', View::POS_END);
$this->registerJs('$("img").unveil();', View::POS_READY);

echo Html::tag('h1', $this->title);

foreach($tabs as $tab => $tabvalue) :
	$tabdata[] = Html::a($tabvalue['shortdesc'], "#{$tab}", ['aria-controls' => $tab, 'aria-selected' => ($tab === array_key_first($tabs)) ? 'true' : 'false', 'class' => ($tab === array_key_first($tabs)) ? 'nav-link active' : 'nav-link', 'data-toggle' => 'tab', 'id' => "{$tab}-tab", 'role' => 'tab']);
endforeach;

echo Html::ul($tabdata, ['class' => 'nav nav-tabs', 'id' => 'nav-tabs', 'encode' => false, 'itemOptions' => ['class' => 'nav-item'], 'role' => 'tablist']);

echo Html::beginTag('div', ['class' => 'tab-content']);
	foreach($tabs as $tab => $tabvalue) :
		echo Html::beginTag('div', ['aria-labelledby' => "{$tab}-tab", 'class' => ($tab === array_key_first($tabs)) ? 'tab-pane fade show active' : 'tab-pane fade', 'id' => $tab, 'role' => 'tabpanel']);
			echo Html::beginTag('div', ['class' => 'row']);
				foreach($recurrence as $r) :
					foreach($hosts as $host) :
						echo Html::tag('div',
							Html::img(null, ['alt' => "{$tabvalue['desc']} of {$host} (last {$r})", 'class' => 'img-fluid mb-2', 'data-src' => Yii::getAlias("@assets/pi/{$host}_{$tab}_{$r}.png"),'height' => 340, 'width' => 540])
						, ['class' => 'col-md-6']);
					endforeach;
					if ($host !== array_key_last($hosts)) :
						echo Html::tag('div', null, ['class' => 'w-100']);
					endif;
				endforeach;
			echo Html::endTag('div');
		echo Html::endTag('div');
	endforeach;
echo Html::endTag('div');
