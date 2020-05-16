<?php

use mister42\assets\ClipboardJsAsset;
use mister42\models\ActiveForm;
use yii\bootstrap4\Html;

$this->title = Yii::t('mr42', 'Lorem Ipsum Generator');
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Tools');
$this->params['breadcrumbs'][] = $this->title;

ClipboardJsAsset::register($this);

for ($x = 5; $x <= 250; $x  = $x + 5) {
    $length[$x] = $x;
}

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-lg-8 mx-auto']);
        echo Html::tag('h1', $this->title);
        echo Html::tag('div', Yii::t('mr42', 'In publishing and graphic design, lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used before final copy is available, but it may also be used to temporarily replace copy in a process called greeking, which allows designers to consider form without the meaning of the text influencing the design.'), ['class' => 'alert alert-info']);

        $form = ActiveForm::begin();
        $tab = 0;

        if ($text = Yii::$app->session->getFlash('lorem-ipsum-success')) {
            echo Html::beginTag('div', ['class' => 'form-group loremipsum-text']);
            echo Html::beginTag('div', ['class' => 'input-group loremipsum-text']);
            echo Yii::$app->icon->activeFieldIcon('heading');
            echo Html::textArea('password', $text, ['class' => 'form-control', 'id' => 'text', 'readonly' => true, 'rows' => '12']);
            echo Html::beginTag('span', ['class' => 'input-group-append']);
            echo Html::button(Yii::$app->icon->name('copy'), ['class' => 'btn btn-primary clipboard-js-init', 'data-clipboard-target' => '#text', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => Yii::t('mr42', 'Copy to Clipboard')]);
            echo Html::endTag('span');
            echo Html::endTag('div');
            echo Html::endTag('div');
        }

        echo Html::beginTag('div', ['class' => 'row form-group']);
            echo $form->field($model, 'amount', [
                'icon' => 'list-ol',
                'options' => ['class' => 'col-sm-6'],
            ])->dropDownList($length, [
                'tabindex' => ++$tab,
            ]);

            echo $form->field($model, 'type', [
                'icon' => 'paragraph',
                'options' => ['class' => 'col-sm-6'],
            ])->dropDownList($model->getTypes(), [
                'prompt' => Yii::t('mr42', 'Select a Type'),
                'tabindex' => ++$tab,
            ]);
        echo Html::endTag('div');

        echo $form->submitToolbar(Yii::t('mr42', 'Generate Text'), $tab);
        ActiveForm::end();
    echo Html::endTag('div');
echo Html::endTag('div');
