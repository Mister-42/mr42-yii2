<?php
use app\assets\ClipboardJsAsset;
use app\models\Icon;
use app\models\calculator\Wpapsk;
use yii\bootstrap4\{ActiveForm, Html, Progress};
use yii\web\View;

$this->title = 'Wifi Protected Access Pre-Shared Key (WPA-PSK) Calculator';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = 'Wifi Protected Access Pre-Shared Key';

$model = new Wpapsk;

ClipboardJsAsset::register($this);
$this->registerJs(Yii::$app->formatter->jspack('calculator/wpapsk.js'), View::POS_HEAD);
$this->registerJs('reset_psk();', View::POS_READY);
$this->registerJs('$("input").keypress(function(e){if(e.which==13){cal_psk();return false}});', View::POS_READY);
$this->registerJs(Yii::$app->formatter->jspack('togglePassword.js'), View::POS_READY);
$this->registerJs(Yii::$app->formatter->jspack('calculator/pbkdf2.js'), View::POS_END);
$this->registerJs(Yii::$app->formatter->jspack('calculator/sha1.js'), View::POS_END);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', Html::tag('abbr', 'WPA', ['title' => 'Wifi Protected Access']).'-'.Html::tag('abbr', 'PSK', ['title' => 'Pre-Shared Key']).' Calculator');
		echo Html::beginTag('div', ['class' => 'alert alert-info']);
			echo Html::tag('div', 'This WPA-PSK calculator provides an easy way to convert a '.Html::tag('abbr', 'SSID', ['title' => 'Service Set Identifier']).' and WPA Passphrase to the 256-bit pre-shared ("raw") key used for key derivation.');
			echo Html::tag('div', 'Type or paste in your SSID and WPA Passphrase below. Click '.Html::tag('span', 'Calculate', ['class' => 'font-italic']).' and wait a while as JavaScript isn\'t known for its blistering cryptographic speed. The Pre-Shared Key will be calculated by your browser. '.Html::tag('b', 'None').' of this information will be sent over the network.');
		echo Html::endTag('div');

		$form = ActiveForm::begin([
			'action' => false,
			'id' => 'wpapsk',
		]);

		echo Html::beginTag('div', ['class' => 'row']);
			echo $form->field($model, 'ssid', [
					'options' => ['class' => 'form-group col-md-6'],
					'template' => '{label}<div class="input-group">'.Icon::fieldAddon('wifi').'{input}</div>{error}',
				])
				->textInput(['tabindex' => ++$tab]);

			echo $form->field($model, 'pass', [
					'options' => ['class' => 'form-group col-md-6'],
					'template' => '{label}<div class="input-group" id="pwdToggle">'.Icon::fieldAddon('lock').'{input}<span class="input-group-append">'.Html::button(Icon::show('eye', ['class' => 'append']).Icon::show('eye-slash', ['class' => 'd-none append']), ['class' => 'btn btn-primary', 'title' => 'Show Password']).'</span></div>{error}',
				])
				->passwordInput(['tabindex' => ++$tab]);
		echo Html::endTag('div');

		echo $form->field($model, 'psk', [
				'options' => ['class' => 'form-group has-success'],
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('key').'{input}<span class="input-group-append">'.Html::button(Icon::show('copy'), ['class' => 'btn btn-primary clipboard-js-init', 'data-clipboard-target' => '#wpapsk-psk', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Copy to Clipboard']).'</span></div>{error}',
			])
			->textInput(['placeholder' => 'JavaScript is disabled in your web browser. This tool does not work without JavaScript.', 'readonly' => true]);

		echo Html::tag('div',
			Html::tag('label', 'Progress') .
			Progress::widget([
				'options' => ['class' => 'progress-bar progress-bar-striped progress-bar-animated']
			])
		, ['class' => 'd-none form-group current-progress']);

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default ml-1 suppress', 'tabindex' => $tab + 2, 'onclick' => 'reset_psk()']) .
			Html::button('Calculate', ['class' => 'btn btn-primary ml-1 suppress', 'tabindex' => ++$tab, 'onclick' => 'cal_psk()'])
		, ['class' => 'btn-toolbar float-right form-group']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
