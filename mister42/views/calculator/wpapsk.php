<?php
use app\assets\ClipboardJsAsset;
use yii\bootstrap\{ActiveForm, Html, Progress};
use yii\web\View;

$this->title = 'Wifi Protected Access Pre-Shared Key Calculator';
$this->params['breadcrumbs'][] = 'Calculator';
$this->params['breadcrumbs'][] = 'Wifi Protected Access Pre-Shared Key';

ClipboardJsAsset::register($this);
$this->registerJs(Yii::$app->formatter->jspack('calculator/wpapsk.js'), View::POS_HEAD);
$this->registerJs('reset_psk();', View::POS_READY);
$this->registerJs('$("input").keypress(function(e){if(e.which==13){cal_psk();return false}});', View::POS_READY);
$this->registerJs(Yii::$app->formatter->jspack('calculator/pbkdf2.js'), View::POS_END);
$this->registerJs(Yii::$app->formatter->jspack('calculator/sha1.js'), View::POS_END);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-offset-2 col-md-8']);
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::beginTag('div', ['class' => 'alert alert-info']);
			echo Html::tag('p', 'This Wifi Protected Access Pre-Shared Key (WPA PSK) calculator provides an easy way to convert a SSID and WPA Passphrase to the 256-bit pre-shared ("raw") key used for key derivation.');
			echo Html::tag('p', 'Type or paste in your SSID and WPA Passphrase below. Click \'Calculate\' and wait a while as JavaScript isn\'t known for its blistering cryptographic speed. The Pre-Shared Key will be calculated by your browser. <strong>None</strong> of this information will be sent over the network.');
		echo Html::endTag('div');

		$form = ActiveForm::begin([
			'action' => false,
			'id' => 'wpapsk',
		]);

		echo $form->field($model, 'ssid', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('signal').'</span>{input}</div>{error}',
			])
			->textInput(['autofocus' => true, 'tabindex' => 1]);

		echo $form->field($model, 'pass', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
			])
			->textInput(['tabindex' => 2]);

		echo $form->field($model, 'psk', [
				'options' => ['class' => 'form-group has-success'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('share').'</span>{input}<span class="input-group-btn">' . Html::button(Html::icon('copy'), ['class' => 'btn btn-primary clipboard-js-init', 'data-clipboard-target' => '#wpapsk-psk', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Copy to Clipboard']) . '</span></div>{error}',
			])
			->textInput(['placeholder' => 'JavaScript is disabled in your web browser. This tool does not work without JavaScript.', 'readonly' => true]);

		echo Progress::widget([
			'barOptions' => ['class' => 'progress-bar-info'],
			'options' => ['class' => 'active hidden progress-striped']
		]);

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 4, 'onclick' => 'reset_psk()']) .
			Html::button('Calculate', ['class' => 'btn btn-primary', 'tabindex' => 3, 'onclick' => 'cal_psk()'])
		, ['class' => 'btn-toolbar form-group pull-right']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
