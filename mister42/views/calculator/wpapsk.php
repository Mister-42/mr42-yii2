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
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8"><?php
		echo Html::tag('h1', Html::encode($this->title));
		echo Html::tag('p', 'This Wifi Protected Access Pre-Shared Key (WPA PSK) calculator provides an easy way to convert a SSID and WPA&nbsp;Passphrase to the 256-bit pre-shared ("raw") key used for key derivation.<br>Type or paste in your SSID and WPA&nbsp;Passphrase below. Click \'Calculate\' and wait a while as JavaScript isn\'t known for its blistering cryptographic speed. The Pre-Shared Key will be calculated by your browser. <b>None</b> of this information will be sent over the network.');

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
			->textInput(['tabindex' => 2]) ?>

		<div class="form-group field-psk">
			<?= Html::tag('label', 'Pre-Shared Key', ['class' => 'control-label']) ?>
			<div class="row">
				<div class="col-md-12">
					<?= Html::tag('div', 'JavaScript is disabled in your web browser. This tool does not work without JavaScript.', ['id' => 'psk']) ?>
				</div>
				<div class="col-md-1 text-right">
					<button class="btn btn-sm btn-primary clipboard-js-init hidden" data-clipboard-target="#psk" data-toggle="tooltip" data-placement="top" title="Copy to Clipboard" type="button"><?= Html::icon('copy') ?></button>
				</div>
			</div>
		</div>

		<div class="form-group text-right">
			<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 4, 'onclick' => 'reset_psk()']) ?>
			<?= Html::button('Calculate', ['class' => 'btn btn-primary', 'tabindex' => 3, 'onclick' => 'cal_psk()']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
</div>
