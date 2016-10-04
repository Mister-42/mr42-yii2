<?php
use yii\bootstrap\{ActiveForm, Html};
?>
<div class="post-form">
	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'title')->textInput(['maxlength' => 255, 'tabindex' => 1]) ?>

	<?= $form->field($model, 'url')->textInput(['maxlength' => 255, 'tabindex' => 2]) ?>

	<?= $form->field($model, 'content')->textarea(['rows' => 6, 'tabindex' => 3]) ?>

	<?= $form->field($model, 'tags')->textInput(['maxlength' => 255, 'tabindex' => 4]) ?>

	<?= $form->field($model, 'active')->checkbox(['tabindex' => 5]) ?>

	<div class="form-group text-right">
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 7]) ?>
		<?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'tabindex' => 6]) ?>
	</div>

	<?php ActiveForm::end(); ?>
</div>
