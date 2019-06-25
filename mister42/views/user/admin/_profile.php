<?php

use app\models\ActiveForm;
use yii\bootstrap4\Html;

$this->beginContent('@Da/User/resources/views/admin/update.php', ['user' => $user]);

$form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-md-3',
            'wrapper' => 'col-md-9',
        ],
    ],
]);

echo $form->field($profile, 'name')->textInput(['tabindex' => ++$tab]);

echo $form->field($profile, 'website')->textInput(['tabindex' => ++$tab]);

echo $form->field($profile, 'lastfm')->textInput(['tabindex' => ++$tab]);

echo $form->field($profile, 'location')->textInput(['tabindex' => ++$tab]);

echo $form->field($profile, 'bio')->textarea(['rows' => 8, 'tabindex' => ++$tab]);

echo Html::tag(
    'div',
    Html::tag(
        'div',
        Html::submitButton(Yii::t('usuario', 'Update'), ['class' => 'btn btn-block btn-success', 'tabindex' => ++$tab]),
        ['class' => 'col-md-9 ml-auto btn-toolbar float-right']
    ),
    ['class' => 'form-group row']
);

ActiveForm::end();

$this->endContent();
