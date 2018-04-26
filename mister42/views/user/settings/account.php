<?php
use app\models\Icon;
use yii\bootstrap4\{ActiveForm, Html};

$this->title = Yii::t('usuario', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('@Da/User/resources/views/shared/_alert', ['module' => Yii::$app->getModule('user')]);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::tag('div',
		$this->render('@Da/User/resources/views/settings/_menu')
	, ['class' => 'col-3']);
	echo Html::beginTag('div', ['class' => 'col-9']);
		echo Html::tag('h3', $this->title);

		$form = ActiveForm::begin([
			'enableAjaxValidation' => true,
			'enableClientValidation' => false,
			'fieldConfig' => [
				'template' => '{label}{beginWrapper}{input}{hint}{error}{endWrapper}',
				'horizontalCssClasses' => [
					'error' => '',
					'hint' => '',
					'label' => 'col-3',
					'wrapper' => 'col-9',
				],
			],
			'layout' => 'horizontal',
		]);

		echo $form->field($model, 'email', [
			'inputTemplate' => '<div class="input-group">'.Icon::fieldAddon('envelope').'{input}</div>',
		])->textInput(['tabindex' => ++$tab]);

		echo $form->field($model, 'username', [
			'inputTemplate' => '<div class="input-group">'.Icon::fieldAddon('user').'{input}</div>',
		])->textInput(['tabindex' => ++$tab]);

		echo $form->field($model, 'new_password', [
			'inputTemplate' => '<div class="input-group">'.Icon::fieldAddon('lock').'{input}</div>',
		])->passwordInput(['tabindex' => ++$tab]);

		echo '<hr>';

		echo $form->field($model, 'current_password', [
			'inputTemplate' => '<div class="input-group">'.Icon::fieldAddon('lock').'{input}</div>',
		])->passwordInput(['tabindex' => ++$tab]);

		echo Html::tag('div',
			Html::submitButton(Yii::t('usuario', 'Save'), ['class' => 'btn btn-success', 'tabindex' => ++$tab])
		, ['class' => 'float-right']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
