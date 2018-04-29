<?php
use app\models\Icon;
use app\assets\ClipboardJsAsset;
use yii\bootstrap4\Html;
use yii\web\View;

$this->title = 'Random Password Generator';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

ClipboardJsAsset::register($this);
$this->registerJs(Yii::$app->formatter->jspack('tools/genpass.js'), View::POS_HEAD);
$this->registerJs('$("[name=\'length\']").change(function(){get();}).change();', View::POS_READY);

for ($x=6; $x<=64; $x++)
	$passLength[$x] = $x;

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', 'This ' . $this->title . ' provides an easy way to create a random password. Password generation is done client-side (on your computer) using JavaScript. <strong>None</strong> of this information will be sent over the network.', ['class' => 'alert alert-info']);

		echo Html::beginTag('form', ['class' => 'passform']);
			echo Html::beginTag('div', ['class' => 'form-group passform-length']);
				echo Html::label('Password Length', null, ['class' => 'control-label']);
				echo Html::beginTag('div', ['class' => 'input-group']);
					echo Html::tag('div',
						Html::tag('span', Icon::show('th-list'), ['class' => 'input-group-text'])
					, ['class' => 'input-group-prepend']);
					echo Html::dropDownList('length', 12, $passLength, ['class' => 'form-control']);
				echo Html::endTag('div');
			echo Html::endTag('div');

			echo Html::beginTag('div', ['class' => 'form-group passform-password']);
				echo Html::label('Password', null, ['class' => 'control-label']);
				echo Html::beginTag('div', ['class' => 'input-group passform-password']);
					echo Html::tag('div',
						Html::tag('span', Icon::show('lock'), ['class' => 'input-group-text'])
					, ['class' => 'input-group-prepend']);
					echo Html::textInput('password', null, ['class' => 'form-control', 'id' => 'password', 'placeholder' => 'JavaScript is disabled in your web browser. This tool does not work without JavaScript.', 'readonly' => true]);
					echo Html::tag('span',
						Html::button(Icon::show('copy'), ['class' => 'btn btn-primary clipboard-js-init', 'data-clipboard-target' => '#password', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Copy to Clipboard'])
					, ['class' => 'input-group-append']);
				echo Html::endTag('div');
			echo Html::endTag('div');

			echo Html::tag('div',
				Html::button('Generate Password', ['class' => 'btn btn-block btn-primary', 'onclick' => 'get()'])
			, ['class' => 'btn-toolbar form-group']);
		echo Html::endTag('form');
	echo Html::endTag('div');
echo Html::endTag('div');
