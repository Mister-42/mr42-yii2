<?php
use yii\bootstrap4\{ActiveForm, Html};

$this->beginContent('@Da/User/resources/views/admin/update.php', ['user' => $user]);

$form = ActiveForm::begin(
	[
		'layout' => 'horizontal',
		'enableAjaxValidation' => true,
		'enableClientValidation' => false,
		'fieldConfig' => [
			'horizontalCssClasses' => [
				'wrapper' => 'col-sm-9',
			],
		],
	]
); ?>

<?= $form->field($profile, 'name') ?>
<?= $form->field($profile, 'website') ?>
<?= $form->field($profile, 'lastfm') ?>
<?= $form->field($profile, 'location') ?>
<?= $form->field($profile, 'bio')->textarea(['rows' => 8, 'tabindex' => 6]) ?>

<div class="form-group">
	<div class="col-md-12 col-lg-9 mx-auto">
		<?= Html::submitButton(Yii::t('usuario', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
	</div>
</div>

<?php ActiveForm::end();

$this->endContent();
