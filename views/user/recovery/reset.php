<?php
use yii\bootstrap\{ActiveForm, Html};

$this->title = 'Reset your password';
$this->params['breadcrumbs'][] = 'User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3"><?php
		echo Html::tag('h2', Html::encode($this->title));

		$form = ActiveForm::begin([
			'id'						=> 'password-recovery-form',
			'enableAjaxValidation'		=> true,
			'enableClientValidation'	=> false,
		]);

		echo $form->field($model, 'password', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
		])->passwordInput();

		echo Html::submitButton('Finish', ['class' => 'btn btn-primary btn-block']) . '<br>';

		ActiveForm::end();
	?></div>
</div>
