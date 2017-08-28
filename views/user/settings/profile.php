<?php
use Da\User\Helper\TimezoneHelper;
use yii\bootstrap\{ActiveForm, Html};
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use yii\web\View;

$this->title = Yii::t('usuario', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;
$timezoneHelper = $model->make(TimezoneHelper::class);

$rules = $model->rules();
$this->registerJs(Yii::$app->formatter->jspack('formCharCounter.js', ['%max%' => $rules['bioString']['max']]), View::POS_READY);
?>

<div class="clearfix"></div>

<?= $this->render('@Da/User/resources/views/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-md-3">
		<?= $this->render('@Da/User/resources/views/settings/_menu') ?>
	</div>
	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?= Html::encode($this->title) ?>
			</div>
			<div class="panel-body"><?php
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
					'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => 5],
				]);

				echo $form->field($model, 'bio', [
						'template' => '{label}<div class="col-lg-9"><div id="chars" class="pull-right"></div></div><div class="input-group"><span class="input-group-addon">'.Html::icon('info-sign').'</span>{input}</div> <div class="col-lg-offset-3 col-lg-9">{hint} {error}</div>'
					])
					->textArea(['id' => 'formContent', 'rows' => 8, 'tabindex' => 6])
					->hint('You may use ' . Html::a('Markdown Syntax', ['/articles/index', 'id' => 4], ['target' => '_blank']) . ' and <code>%age%</code> to show your age, calculated from <nobr>' . Html::tag('code', $model->getAttributeLabel('birthday')) . '</nobr>. HTML is not allowed.');

				echo $form->field($model, 'timezone', [
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('time').'</span>{input}</div>{error}',
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
	</div>
</div>
