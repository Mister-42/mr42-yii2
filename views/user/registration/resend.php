<?php
use yii\bootstrap\{ActiveForm, Html};

$this->title = 'Request new confirmation message';
$this->params['breadcrumbs'][] = 'User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3"><?php
		echo Html::tag('h2', Html::encode($this->title));

		$form = ActiveForm::begin([
			'id'						=> 'resend-form',
			'enableAjaxValidation'		=> true,
			'enableClientValidation'	=> false,
		]);

		echo $form->field($model, 'email', [
			'inputOptions' => ['autofocus' => true, 'tabindex' => 1],
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="addon-email"></span></span>{input}</div>{error}',
		]);

		echo Html::submitButton('Continue', ['class' => 'btn btn-primary btn-block', 'tabindex' => 2]) . '<br>';

		ActiveForm::end();
	?></div>
</div>
