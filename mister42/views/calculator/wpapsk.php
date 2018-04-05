<?php
use app\assets\ClipboardJsAsset;
use yii\base\DynamicModel;
use yii\bootstrap\{ActiveForm, Html};
use yii\web\View;

$this->title = 'Wifi Protected Access Pre-Shared Key Calculator';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = 'Wifi Protected Access Pre-Shared Key';

ClipboardJsAsset::register($this);
$this->registerJs(Yii::$app->formatter->jspack('calculator/wpapsk.js'), View::POS_HEAD);
$this->registerJs('reset_psk();', View::POS_READY);
$this->registerJs('$("form input").keydown(function(e){if(e.keyCode==13){cal_psk();return false}});', View::POS_READY);
$this->registerJs(Yii::$app->formatter->jspack('calculator/pbkdf2.js'), View::POS_END);
$this->registerJs(Yii::$app->formatter->jspack('calculator/sha1.js'), View::POS_END);

$model = new DynamicModel(['ssid', 'pass']);
$model->addRule('ssid', 'required', ['message' => 'SSID cannot be blank.']);
$model->addRule('pass', 'required', ['message' => 'WPA Passphrase cannot be blank.']);
$model->addRule('ssid', 'string', ['max'=>32]);
$model->addRule('pass', 'string', ['min'=>8, 'max'=>63]);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-offset-2 col-md-8']);
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::beginTag('div', ['class' => 'alert alert-info']);
			echo Html::tag('p', 'This Wifi Protected Access Pre-Shared Key (WPA PSK) calculator provides an easy way to convert a SSID and WPA Passphrase to the 256-bit pre-shared ("raw") key used for key derivation.');
			echo Html::tag('p', 'Type or paste in your SSID and WPA Passphrase below. Click \'Calculate\' and wait a while as JavaScript isn\'t known for its blistering cryptographic speed. The Pre-Shared Key will be calculated by your browser. <strong>None</strong> of this information will be sent over the network.');
		echo Html::endTag('div');

		$form = ActiveForm::begin([
				'id' => 'wpapsk',
				'action' => null,
				'options' => ['csrf' => false],
				'fieldConfig' => [
						'template' => "{label}{input}{error}",
						'labelOptions' => ['class' => 'control-label'],
				],
		]);

		echo $form->field($model, 'ssid', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('signal').'</span>{input}</div>{error}',
			])
			->label('SSID')
			->textInput(['autofocus' => true, 'tabindex' => 1]);

		echo $form->field($model, 'pass', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
			])
			->label('WPA Passphrase')
			->textInput(['tabindex' => 2]);

		echo Html::tag('div',
			Html::tag('div', null, ['class' => 'progress-bar progress-bar-striped progress-bar-info active'])
		, ['class' => 'progress']);

		echo Html::beginTag('div', ['class' => 'form-group field-psk']);
			echo Html::label('Pre-Shared Key', null, ['class' => 'control-label']);
			echo Html::beginTag('div', ['class' => 'input-group passform-password']);
				echo Html::tag('span', Html::icon('share'), ['class' => 'input-group-addon']);
				echo Html::textInput('psk', null, ['class' => 'form-control', 'id' => 'psk', 'placeholder' => 'JavaScript is disabled in your web browser. This tool does not work without JavaScript.', 'readonly' => true]);
				echo Html::tag('span',
					Html::button(Html::icon('copy'), ['class' => 'btn btn-primary clipboard-js-init', 'data-clipboard-target' => '#psk', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Copy to Clipboard'])
				, ['class' => 'input-group-btn']);
			echo Html::endTag('div');
		echo Html::endTag('div');

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 4, 'onclick' => 'reset_psk()']) .
			Html::button('Calculate', ['class' => 'btn btn-primary', 'tabindex' => 3, 'onclick' => 'cal_psk()'])
		, ['class' => 'btn-toolbar form-group pull-right']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
