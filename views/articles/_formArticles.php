<?php
use yii\bootstrap\{ActiveForm, Html};

$this->title = $action === 'create' ? 'Create Article' : 'Edit Article';
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
if ($action === 'edit')
	$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['index', 'id' => $model->id, 'title' => $model->url]];
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

$form = ActiveForm::begin();

echo $form->field($model, 'title')->textInput(['maxlength' => 255, 'tabindex' => 1]);

echo $form->field($model, 'url')->textInput(['maxlength' => 255, 'tabindex' => 2]);

echo $form->field($model, 'content')->textarea(['rows' => 6, 'tabindex' => 3]);

echo $form->field($model, 'source')->textInput(['maxlength' => 128, 'tabindex' => 4]);

echo $form->field($model, 'tags')->textInput(['maxlength' => 255, 'tabindex' => 5]);

echo $form->field($model, 'active')->checkbox(['tabindex' => 6]) ?>

<div class="form-group text-right">
	<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 8]) ?>
	<?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'tabindex' => 7]) ?>
</div>

<?php ActiveForm::end(); ?>
