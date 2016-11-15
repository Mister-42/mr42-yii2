<?php
use dektrium\user\helpers\Timezone;
use yii\bootstrap\{ActiveForm, Html};
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use yii\web\View;

$this->title = 'Edit Profile';
$this->params['breadcrumbs'][] = $this->title;

$rules = $model->rules();
$this->registerJs(Yii::$app->formatter->jspack('formCharCounter.js', ['%max%' => $rules['bioString']['max']]), View::POS_READY);
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-md-offset-2 col-md-8"><?php
		echo Html::tag('h1', Html::encode($this->title));

		$form = ActiveForm::begin([
			'enableAjaxValidation' => true,
			'enableClientValidation' => false,
			'fieldConfig' => [
				'labelOptions' => ['class' => 'col-lg-3 control-label'],
			],
			'options' => ['class' => 'form-horizontal'],
			'validateOnBlur' => false,
		]);

		echo $form->field($model, 'name', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
		])->textInput(['tabindex' => 1]);

		echo $form->field($model, 'website', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('globe').'</span>{input}</div>{error}',
		])->input('url', ['tabindex' => 2]);

		echo $form->field($model, 'lastfm', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('music').'</span>{input}</div>{error}',
		])->textInput(['tabindex' => 3]);

		echo $form->field($model, 'location', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('map-marker').'</span>{input}</div>{error}',
		])->textInput(['tabindex' => 4]);

		echo $form->field($model, 'birthday', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('calendar').'</span>{input}</div>{error}',
		])->widget(DatePicker::classname(), [
			'clientOptions' => [
				'changeMonth' => true,
				'changeYear' => true,
				'firstDay' => 1,
				'maxDate' => '-16Y',
				'minDate' => '-110Y',
				'yearRange' => '-110Y:-16Y',
			],
			'dateFormat' => 'yyyy-MM-dd',
			'language' => 'en-GB',
			'options' => ['class' => 'form-control', 'tabindex' => 5],
		]);

		echo $form->field($model, 'bio', [
				'template' => '{label}<div class="col-lg-9"><div id="chars" class="pull-right"></div></div><div class="input-group"><span class="input-group-addon">'.Html::icon('info-sign').'</span>{input}</div> <div class="col-lg-offset-3 col-lg-9">{hint} {error}</div>'
			])
			->textArea(['id' => 'formContent', 'rows' => 8, 'tabindex' => 6])
			->hint('You may use ' . Html::a('Markdown Syntax', ['/articles/index', 'id' => 4], ['target' => '_blank']) . ' and <code>%age%</code> to show your age, calculated from <nobr>' . Html::tag('code', $model->getAttributeLabel('birthday')) . '</nobr>. HTML is not allowed.');

		echo $form->field($model, 'timezone', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('time').'</span>{input}</div>{error}',
		])->dropDownList(ArrayHelper::map(Timezone::getAll(), 'timezone', 'name'), ['tabindex' => 7]); ?>

		<div class="form-group text-right">
			<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 9]) ?>
			<?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'tabindex' => 8]) ?>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
