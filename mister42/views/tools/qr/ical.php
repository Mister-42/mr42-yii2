<?php
use phamxuanloc\jui\DateTimePicker;
use yii\bootstrap\{ActiveForm, Html};
use yii\web\View;

if (!Yii::$app->request->isAjax) {
	$this->registerJs('$("#qr-start").val("' . $model->start . '")', View::POS_READY);
	$this->registerJs('$("#qr-end").val("' . $model->end . '")', View::POS_READY);
}

$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo '<div class="row">';
	$tab = 2;
	foreach (['start', 'end'] as $name) :
		echo $form->field($model, $name, [
			'options' => ['class' => 'col-sm-6'],
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('time').'</span>{input}</div>{error}',
		])->widget(DateTimePicker::className(), [
			'clientOptions' => [
				'changeMonth' => true,
				'changeYear' => true,
				'firstDay' => 1,
				'timeFormat' => 'HH:mm',
			],
			'dateFormat' => 'yyyy-MM-dd',
			'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => $tab++],
		]);
	endforeach;
echo '</div>';

echo $form->field($model, 'summary', [
		'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('comment').'</span>{input}</div>{error}',
	])->textArea(['rows' => 6, 'tabindex' => $tab++]);

echo $model->getFormFooter($form);

ActiveForm::end();
