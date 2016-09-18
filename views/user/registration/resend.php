<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = 'Request new confirmation message';
$this->params['breadcrumbs'][] = 'User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
		<h2><?= Html::encode($this->title) ?></h2>

		<?php $form = ActiveForm::begin([
			'id'								=> 'resend-form',
			'enableAjaxValidation'		=> true,
			'enableClientValidation'	=> false,
		]); ?>

		<?= $form->field($model, 'email', [
			'inputOptions' => ['autofocus' => true, 'tabindex' => 1],
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="addon-email"></span></span>{input}</div>{error}',
		]) ?>

		<?= Html::submitButton('Continue', ['class' => 'btn btn-primary btn-block', 'tabindex' => 2]) ?><br>

		<?php ActiveForm::end(); ?>
	</div>
</div>
