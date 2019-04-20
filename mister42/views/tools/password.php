<?php
use app\assets\ClipboardJsAsset;
use yii\bootstrap4\Html;
use yii\web\View;

$this->title = Yii::t('mr42', 'Password Generator');
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Tools')];
$this->params['breadcrumbs'][] = $this->title;

ClipboardJsAsset::register($this);
$this->registerJs(Yii::$app->formatter->jspack('tools/genpass.js'), View::POS_HEAD);
$this->registerJs('$("[name=\'length\']").change(function(){get();}).change();', View::POS_READY);

for ($x = 6; $x <= 64; $x++) :
	$passLength[$x] = Yii::t('mr42', '{x} characters', ['x' => $x]);
endfor;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', Yii::t('mr42', 'This {title} provides an easy way to create a random password. Password generation is done client-side (on your computer) using JavaScript. <b>None</b> of this information will be sent over the network.', ['title' => $this->title]), ['class' => 'alert alert-info']);

		echo Html::beginTag('form', ['class' => 'passform']);
			echo Html::beginTag('div', ['class' => 'form-group passform-length']);
				echo Html::label(Yii::t('mr42', 'Password Length'), null, ['class' => 'control-label']);
				echo Html::beginTag('div', ['class' => 'input-group']);
					echo Html::tag('div',
						Html::tag('span', Yii::$app->icon->show('th-list'), ['class' => 'input-group-text'])
					, ['class' => 'input-group-prepend']);
					echo Html::dropDownList('length', 12, $passLength, ['class' => 'form-control']);
				echo Html::endTag('div');
			echo Html::endTag('div');

			echo Html::beginTag('div', ['class' => 'form-group passform-password']);
				echo Html::label(Yii::t('mr42', 'Password'), null, ['class' => 'control-label']);
				echo Html::beginTag('div', ['class' => 'input-group passform-password']);
					echo Html::tag('div',
						Html::tag('span', Yii::$app->icon->show('lock'), ['class' => 'input-group-text'])
					, ['class' => 'input-group-prepend']);
					echo Html::textInput('password', null, ['class' => 'form-control', 'id' => 'password', 'placeholder' => Yii::t('mr42', 'JavaScript is disabled in your web browser. This tool does not work without JavaScript.'), 'readonly' => true]);
					echo Html::tag('span',
						Html::button(Yii::$app->icon->show('copy'), ['class' => 'btn btn-primary clipboard-js-init', 'data-clipboard-target' => '#password', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => Yii::t('mr42', 'Copy to Clipboard')])
					, ['class' => 'input-group-append']);
				echo Html::endTag('div');
			echo Html::endTag('div');

			echo Html::tag('div',
				Html::button(Yii::t('mr42', 'Generate Password'), ['class' => 'btn btn-block btn-primary', 'onclick' => 'get()'])
			, ['class' => 'btn-toolbar form-group']);
		echo Html::endTag('form');
	echo Html::endTag('div');
echo Html::endTag('div');
