<?php
use app\models\Icon;
use yii\bootstrap4\{ActiveForm, Html};

$tab = 1;
$form = ActiveForm::begin(['id' => Yii::$app->request->post('type')]);

echo $form->field($model, 'type')->hiddenInput()->label(false);

echo Html::beginTag('div', ['class' => 'row form-group']);
	foreach (['lat', 'lng', 'altitude'] as $name) :
		echo $form->field($model, $name, [
				'options' => ['class' => 'col-md-4'],
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('globe').'{input}</div>{error}',
			])->input('number', ['step' => '0.000001', 'tabindex' => ++$tab]);
	endforeach;
echo Html::endTag('div');

echo $model->getFormFooter($form, $tab);

ActiveForm::end();
