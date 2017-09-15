<?php
use Da\User\Helper\TimezoneHelper;
use janisto\timepicker\TimePicker;
use yii\bootstrap\{ActiveForm, Html};
use yii\helpers\ArrayHelper;
use yii\web\View;

$this->title = Yii::t('usuario', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;
$timezoneHelper = $model->make(TimezoneHelper::class);

$this->registerJs(Yii::$app->formatter->jspack('formCharCounter.js', ['%max%' => $model->rules()['bioString']['max']]), View::POS_READY);
?>

<div class="clearfix"></div>

<?= $this->render('@Da/User/resources/views/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-md-3">
		<?= $this->render('@Da/User/resources/views/settings/_menu') ?>
	</div>
	<div class="col-md-9"><?php
		echo Html::tag('h3', Html::encode($this->title));

		$form = ActiveForm::begin([
			'enableAjaxValidation' => true,
			'enableClientValidation' => false,
			'fieldConfig' => [
				'template' => '{label}{beginWrapper}{input}{hint}{error}{endWrapper}',
				'horizontalCssClasses' => [
					'error' => '',
					'hint' => '',
					'label' => 'col-lg-3',
					'wrapper' => 'col-lg-9',
				],
			],
			'layout' => 'horizontal',
			'validateOnBlur' => false,
		]);

		echo $form->field($model, 'name', [
			'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>',
		])->textInput(['tabindex' => 1]);

		echo $form->field($model, 'website', [
			'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Html::icon('globe').'</span>{input}</div>',
		])->input('url', ['tabindex' => 2]);

		echo $form->field($model, 'lastfm', [
			'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Html::icon('music').'</span>{input}</div>',
		])->textInput(['tabindex' => 3]);

		echo $form->field($model, 'location', [
			'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Html::icon('map-marker').'</span>{input}</div>',
		])->textInput(['tabindex' => 4]);

		echo $form->field($model, 'birthday')->widget(TimePicker::classname(), [
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
			'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => 5],
		]);

		echo $form->field($model, 'bio', [
				'inputTemplate' => '<div class="row"><div id="chars" class="col-lg-12 text-right"></div></div><div class="input-group"><span class="input-group-addon">'.Html::icon('info-sign').'</span>{input}</div>',
			])
			->textArea(['id' => 'formContent', 'rows' => 8, 'tabindex' => 6])
			->hint('You may use ' . Html::a('Markdown Syntax', ['/articles/index', 'id' => 4], ['target' => '_blank']) . ' and <code>%age%</code> to show your age, calculated from <nobr>' . Html::tag('code', $model->getAttributeLabel('birthday')) . '</nobr>. HTML is not allowed.');

		echo $form->field($model, 'timezone', [
			'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Html::icon('time').'</span>{input}</div>',
		])->dropDownList(ArrayHelper::map($timezoneHelper->getAll(), 'timezone', 'name'), ['tabindex' => 7]); ?>

		<div class="form-group">
			<div class="col-lg-offset-3 col-lg-9">
				<?= Html::submitButton(Yii::t('usuario', 'Save'), ['class' => 'btn btn-block btn-success']) ?>
				<br>
			</div>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
</div>
