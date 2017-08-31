<?php
use yii\bootstrap\{ActiveForm, Html};

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
	<div class="col-lg-offset-3 col-lg-9">
		<?= Html::submitButton(Yii::t('usuario', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
	</div>
</div>

<?php ActiveForm::end();

$this->endContent();
