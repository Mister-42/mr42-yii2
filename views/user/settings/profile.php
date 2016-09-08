<?php
use dektrium\user\helpers\Timezone;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\web\View;

$this->title = 'Edit Profile';
$this->params['breadcrumbs'][] = $this->title;

$rules = $model->rules();
$this->registerJs('$(\'#formContent\').keyup(function(){len=$(this).val().length;char='.$rules['bioString']['max'].'-len;if(len>'.$rules['bioString']['max'].'){$(\'#chars\').text(\'You are \'+Math.abs(char)+\' characters over the limit.\').addClass(\'alert-danger\')}else{$(\'#chars\').text(\'You have \'+char+\' characters left\').removeClass(\'alert-danger\');}}).keyup();', View::POS_READY);
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<?= Html::tag('h1', Html::encode($this->title)) ?>

		<?php $form = ActiveForm::begin([
			'id' => 'profile-form',
			'options' => ['class' => 'form-horizontal'],
			'fieldConfig' => [
				'labelOptions' => ['class' => 'col-lg-3 control-label'],
			],
			'enableAjaxValidation' => true,
			'enableClientValidation' => false,
			'validateOnBlur' => false,
		]);

		echo $form->field($model, 'name', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>{input}</div>{error}',
		])->textInput(['tabindex' => 1]);

		echo $form->field($model, 'website', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-globe"></span></span>{input}</div>{error}',
		])->textInput(['tabindex' => 2]);

		echo $form->field($model, 'lastfm', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-music"></span></span>{input}</div>{error}',
		])->textInput(['tabindex' => 3]);

		echo $form->field($model, 'location', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-map-marker"></span></span>{input}</div>{error}',
		])->textInput(['tabindex' => 4]);

		echo $form->field($model, 'birthday', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>{input}</div>{error}',
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
				'template' => '{label}<div class="col-lg-9"><div id="chars" class="pull-right"></div></div><div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>{input}</div> <div class="col-lg-offset-3 col-lg-9">{hint} {error}</div>'
			]) 
			->textArea(['id' => 'formContent', 'rows' => 8, 'tabindex' => 6])
			->hint('You may use ' . Html::a('Markdown Syntax', ['/post/index', 'id' => 4], ['target' => '_blank']) . ' and <code>%age%</code> to show your age, calculated from <nobr>' . Html::tag('code', $model->getAttributeLabel('birthday')) . '</nobr>. HTML is not allowed.');

		echo $form->field($model, 'timezone', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>{input}</div>{error}',
		])->dropDownList(ArrayHelper::map(Timezone::getAll(), 'timezone', 'name'), ['tabindex' => 7]); ?>

		<div class="form-group">
			<div class="text-right">
				<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 9]) ?>
				<?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'tabindex' => 8]) ?>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
