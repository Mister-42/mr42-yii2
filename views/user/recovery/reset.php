<?php
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

$this->title = 'Reset your password';
$this->params['breadcrumbs'][] = 'User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
		<h2><?= Html::encode($this->title) ?></h2>

		<?php $form = ActiveForm::begin([
			'id'								=> 'password-recovery-form',
			'enableAjaxValidation'		=> true,
			'enableClientValidation'	=> false,
		]); ?>

		<?= $form->field($model, 'password', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
		])->passwordInput() ?>

		<?= Html::submitButton('Finish', ['class' => 'btn btn-primary btn-block']) ?><br>

		<?php ActiveForm::end(); ?>
	</div>
</div>
