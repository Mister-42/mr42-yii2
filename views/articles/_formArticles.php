<?php
use yii\bootstrap\{ActiveForm, Html};

$form = ActiveForm::begin();

echo $form->field($model, 'title')->textInput(['maxlength' => 255, 'tabindex' => 1]);

echo $form->field($model, 'url')->textInput(['maxlength' => 255, 'tabindex' => 2]);

echo $form->field($model, 'content')->textarea(['rows' => 6, 'tabindex' => 3]);

echo $form->field($model, 'sourceName')->textInput(['maxlength' => 25, 'tabindex' => 4]);

echo $form->field($model, 'sourceUrl')->textInput(['maxlength' => 128, 'tabindex' => 5]);

echo $form->field($model, 'tags')->textInput(['maxlength' => 255, 'tabindex' => 6]);

echo $form->field($model, 'active')->checkbox(['tabindex' => 7]) ?>

<div class="form-group text-right">
	<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 9]) ?>
	<?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'tabindex' => 8]) ?>
</div>

<?php ActiveForm::end(); ?>
