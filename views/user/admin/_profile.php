<?php
use dektrium\user\helpers\Timezone;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;

$this->beginContent('@dektrium/user/views/admin/update.php', ['user' => $user]);

$form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-9',
        ],
    ],
]);

echo $form->field($profile, 'name')->textInput(['tabindex' => 1]);

echo $form->field($profile, 'website')->textInput(['tabindex' => 2]);

echo $form->field($profile, 'lastfm')->textInput(['tabindex' => 3]);

echo $form->field($profile, 'location')->textInput(['tabindex' => 4]);

echo $form->field($profile, 'birthday')->widget(DatePicker::classname(), [
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

echo $form->field($profile, 'bio', [
		'template' => '{label}<div class="col-lg-9"><div><div id="chars" class="pull-right"></div>{input}</div> {hint}</div><div class="col-lg-offset-2 col-lg-6">{error}</div>'
	]) 
	->textArea(['id' => 'formContent', 'rows' => 8, 'tabindex' => 6]);

echo $form->field($profile, 'timezone')->dropDownList(ArrayHelper::map(Timezone::getAll(), 'timezone', 'name'), ['tabindex' => 7]);
?>

<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?= Html::submitButton('Update', ['class' => 'btn btn-block btn-primary', 'tabindex' => 8]) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
