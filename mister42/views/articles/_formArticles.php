<?php
use yii\bootstrap4\{ActiveForm, Html};

$this->title = $action === 'create' ? 'Create Article' : 'Edit Article';
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
if ($action === 'edit') :
	$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['index', 'id' => $model->id, 'title' => $model->url]];
endif;
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', $this->title);

$form = ActiveForm::begin();
$tab = 0;

echo $form->field($model, 'title')->textInput(['maxlength' => 255, 'tabindex' => ++$tab]);

echo $form->field($model, 'url')->textInput(['maxlength' => 255, 'tabindex' => ++$tab]);

echo $form->field($model, 'content')->textarea(['rows' => 6, 'tabindex' => ++$tab]);

echo $form->field($model, 'source')->input('url', ['maxlength' => 128, 'tabindex' => ++$tab]);

echo $form->field($model, 'tags')->textInput(['maxlength' => 255, 'tabindex' => ++$tab]);

echo $form->field($model, 'pdf')->checkbox(['tabindex' => ++$tab]);

echo $form->field($model, 'active')->checkbox(['tabindex' => ++$tab]);

echo Html::tag('div',
	Html::resetButton('Reset', ['class' => 'btn btn-default ml-1', 'tabindex' => $tab + 2]).
	Html::submitButton('Save', ['class' => 'btn btn-primary ml-1', 'id' => 'pjaxtrigger', 'tabindex' => ++$tab])
, ['class' => 'btn-toolbar float-right form-group']);

ActiveForm::end();
