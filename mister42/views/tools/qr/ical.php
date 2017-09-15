<?php
use janisto\timepicker\TimePicker;
use yii\bootstrap\{ActiveForm, Html};
use yii\web\View;

$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);
$tab = 2;

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo '<div class="row">';
	foreach (['start', 'end'] as $name) :
		echo $form->field($model, $name, [
			'options' => ['class' => 'col-sm-6'],
		])->widget(TimePicker::className(), [
			'clientOptions' => [
				'changeMonth' => true,
				'changeYear' => true,
				'dateFormat' => 'yy-mm-dd',
				'firstDay' => 1,
				'timeFormat' => 'HH:mm',
			],
			'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => $tab++],
		]);
	endforeach;
echo '</div>';

echo $form->field($model, 'summary', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('comment').'</span>{input}</div>{error}',
	])->textArea(['rows' => 6, 'tabindex' => $tab++]);

echo $model->getFormFooter($form);

ActiveForm::end();
