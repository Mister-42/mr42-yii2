<?php
use app\models\Form;
use app\widgets\TimePicker;
use Da\User\Helper\TimezoneHelper;
use yii\bootstrap4\{ActiveForm, Html};
use yii\helpers\ArrayHelper;

$this->title = Yii::t('usuario', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;
$timezoneHelper = $model->make(TimezoneHelper::class);

Form::charCount($this, $model->rules()['bioString']['max']);

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
					'label' => 'col-md-3',
					'wrapper' => 'col-md-9',
				],
			],
			'layout' => 'horizontal',
			'validateOnBlur' => false,
		]);
		$tab = 0;

		echo $form->field($model, 'name', [
			'inputTemplate' => '<div class="input-group">'.Yii::$app->icon->fieldAddon('user').'{input}</div>',
		])->textInput(['tabindex' => ++$tab]);

		echo $form->field($model, 'website', [
			'inputTemplate' => '<div class="input-group">'.Yii::$app->icon->fieldAddon('globe').'{input}</div>',
		])->input('url', ['tabindex' => ++$tab]);

		echo $form->field($model, 'lastfm', [
			'inputTemplate' => '<div class="input-group">'.Yii::$app->icon->fieldAddon('lastfm', ['prefix' => 'fab fa-']).'{input}</div>',
		])->textInput(['tabindex' => ++$tab]);

		echo $form->field($model, 'location', [
			'inputTemplate' => '<div class="input-group">'.Yii::$app->icon->fieldAddon('map-marker').'{input}</div>',
		])->textInput(['tabindex' => ++$tab]);

		echo $form->field($model, 'birthday')->widget(TimePicker::class, [
			'clientOptions' => [
				'changeMonth' => true,
				'changeYear' => true,
				'dateFormat' => 'yy-mm-dd',
				'firstDay' => 1,
				'maxDate' => '-16Y',
				'minDate' => '-110Y',
				'yearRange' => '-110Y:-16Y',
			],
			'mode' => 'date',
			'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => ++$tab],
		]);

		echo $form->field($model, 'bio', [
				'inputTemplate' => '<div class="row"><div id="chars" class="col-12 text-right"></div></div><div class="input-group">'.Yii::$app->icon->fieldAddon('info-circle').'{input}</div>',
			])
			->textArea(['id' => 'formContent', 'rows' => 8, 'tabindex' => ++$tab])
			->hint(Yii::t('mr42', 'You may use {markdown} and {age} to show your age, calculated from <nobr>{birthday}</nobr>. HTML is not allowed.', ['markdown' => Html::a(Yii::t('mr42', 'Markdown Syntax'), ['/permalink/articles', 'id' => 4], ['target' => '_blank']), 'age' => '<code>%age%</code>', 'birthday' => Html::tag('code', $model->getAttributeLabel('birthday'))]));

		echo $form->field($model, 'timezone', [
			'inputTemplate' => '<div class="input-group">'.Yii::$app->icon->fieldAddon('clock').'{input}</div>',
		])->dropDownList(ArrayHelper::map($timezoneHelper->getAll(), 'timezone', 'name'), ['tabindex' => ++$tab]);

		echo Html::tag('div',
			Html::submitButton(Yii::t('usuario', 'Save'), ['class' => 'btn btn-success', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
