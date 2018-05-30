<?php
use app\models\Form;
use app\widgets\TimePicker;
use yii\bootstrap4\{ActiveForm, Alert, Html};
use yii\web\View;

$this->title = Yii::t('mr42', 'Time Zone Converter');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Calculator');
$this->params['breadcrumbs'][] = $this->title;

$model->source = Yii::$app->request->isPost ? $model->source : 'Europe/Berlin';
$model->target = Yii::$app->request->isPost ? $model->target : 'America/New_York';

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);
		echo Html::tag('div', Yii::t('mr42', 'With this calculator you can check the date and time in any timezone around the world.'), ['class' => 'alert alert-info']);

		if ($flash = Yii::$app->session->getFlash('timezone-success')) :
			Alert::begin(['options' => ['class' => 'alert-success fade show']]);
				echo Html::tag('div', Yii::t('mr42', '{a} in {b}', ['a' => Yii::$app->formatter->asDate($model->datetime.' '.Yii::$app->timeZone, 'full').' '.Yii::$app->formatter->asTime($model->datetime.' '.Yii::$app->timeZone, 'short'), 'b' => str_replace('_', ' ', $model->source)]));
				echo Html::tag('div', Yii::t('mr42', 'equals'));
				echo Html::tag('div', Yii::t('mr42', '{a} in {b}', ['a' => Html::tag('strong', Yii::$app->formatter->asDate($flash->format('Y-m-d H:i').' '.Yii::$app->timeZone, 'full').' '.Yii::$app->formatter->asTime($flash->format('Y-m-d H:i').' '.Yii::$app->timeZone, 'short')), 'b' => Html::tag('strong', str_replace('_', ' ', $model->target))]));
			Alert::end();
		endif;

		$form = ActiveForm::begin();
		$tab = 0;

		echo Html::beginTag('div', ['class' => 'row']);
			foreach (['source', 'target'] as $field) :
				echo $form->field($model, $field, [
					'options' => ['class' => 'form-group col-md-6'],
					'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('globe').'{input}</div>{error}',
				])->dropDownList($model->getTimezones(true), ['tabindex' => ++$tab]);
			endforeach;
		echo Html::endTag('div');

		echo Html::beginTag('div', ['class' => 'row']);
			echo $form->field($model, 'datetime', [
				'options' => ['class' => 'form-group col-md-6'],
			])->widget(TimePicker::class, [
				'addon' => 'clock',
				'clientOptions' => [
					'changeMonth' => true,
					'changeYear' => true,
					'dateFormat' => 'yy-mm-dd',
					'firstDay' => 1,
					'timeFormat' => 'HH:mm',
				],
				'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => ++$tab],
			]);
		echo Html::endTag('div');

		echo Form::submitToolbar(Yii::t('mr42', 'Calculate'), $tab);

		ActiveForm::end();
	echo Html::endTag('div');
echo Html::endTag('div');
