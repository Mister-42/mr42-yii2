<?php

use mister42\assets\CharCounterAsset;
use mister42\assets\FileInputAsset;
use mister42\models\ActiveForm;
use Da\User\Widget\ReCaptchaWidget;
use yii\bootstrap4\Html;
use yii\widgets\Pjax;

$this->title = Yii::t('mr42', 'Contact');
$this->params['breadcrumbs'] = [Yii::$app->name];
$this->params['breadcrumbs'][] = $this->title;

CharCounterAsset::register($this, $model->rules()['charCount']['max']);
FileInputAsset::register($this);

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
        echo Html::tag('h1', $this->title);

        Pjax::begin(['enablePushState' => false, 'linkSelector' => 'pjaxtrigger']);
            echo Html::tag('div', Yii::t('mr42', 'If you have inquiries or other questions, please fill out the following form to contact {siteName}. Thank you.', ['siteName' => Yii::$app->name]), ['class' => 'alert alert-info']);

            $form = ActiveForm::begin(['options' => ['data-pjax' => '']]);
            $tab = 0;

            echo '<div class="row">';
                echo $form->field($model, 'name', [
                    'icon' => 'user',
                    'options' => ['class' => 'col-md-6 form-group'],
                ])->textInput(['tabindex' => ++$tab]);

                echo $form->field($model, 'email', [
                    'icon' => 'at',
                    'options' => ['class' => 'col-md-6 form-group'],
                ])->input('email', ['tabindex' => ++$tab]);
            echo '</div>';

            echo $form->field($model, 'title', [
                'icon' => 'heading',
            ])->textInput(['tabindex' => ++$tab]);

            echo $form->field($model, 'content', [
                'inputTemplate' => '<div id="chars" class="float-right"></div>' . Yii::$app->icon->activeFieldAddon('comment'),
            ])->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => ++$tab]);

            echo $form->field($model, 'attachment', [
                'inputTemplate' => '<div class="input-group">' . Yii::$app->icon->activeFieldIcon('paperclip') . Html::tag('div', '{input}' . Html::tag('label', Yii::t('mr42', 'Select a File'), ['class' => 'custom-file-label text-truncate']), ['class' => 'custom-file']) . '</div>',
            ])->fileInput(['class' => 'custom-file-input', 'id' => 'sourceFile', 'tabindex' => ++$tab]);

            echo $form->field($model, 'captcha')->widget(ReCaptchaWidget::class)->label(false);

            echo Html::tag(
                'div',
                Html::resetButton(Yii::t('mr42', 'Reset'), ['class' => 'btn btn-light ml-1', 'tabindex' => $tab + 2]) .
                Html::submitButton(Yii::t('mr42', 'Send'), ['class' => 'btn btn-primary ml-1', 'id' => 'pjaxtrigger', 'tabindex' => ++$tab]),
                ['class' => 'form-group float-right btn-toolbar']
            );

            ActiveForm::end();
        Pjax::end();
    echo Html::endTag('div');
echo Html::endTag('div');
