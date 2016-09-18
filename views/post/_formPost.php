<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
?>
<div class="post-form">
	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'title')->textInput(['maxlength' => 255, 'tabindex' => 1]) ?>

	<?= $form->field($model, 'content')->textarea(['rows' => 6, 'tabindex' => 2]) ?>

	<?= $form->field($model, 'tags')->textInput(['maxlength' => 255, 'tabindex' => 3]) ?>

	<?= $form->field($model, 'active')->checkbox(['tabindex' => 4]) ?>

	<div class="form-group text-right">
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 6]) ?>
		<?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'tabindex' => 5]) ?>
	</div>

	<?php ActiveForm::end(); ?>
</div>
