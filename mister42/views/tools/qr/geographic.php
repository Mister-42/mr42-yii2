<?php
use yii\bootstrap\{ActiveForm, Html};

$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo '<div class="row">';
	$tab = 2;
	foreach (['lat', 'lng', 'altitude'] as $name) :
		echo $form->field($model, $name, [
				'options' => ['class' => 'col-sm-4'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('globe').'</span>{input}</div>{error}',
			])->input('number', ['step' => '0.000001', 'tabindex' => $tab++]);
	endforeach;
echo '</div>';

echo $model->getFormFooter($form);

ActiveForm::end();
