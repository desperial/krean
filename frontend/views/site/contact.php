<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Обратная связь';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sf-main-wrap">
    <div class="sf-overlay">
        <div class="sf-center-content">
        <div class="site-contact">
            <h1 style="padding: 15px 20px;" class="text-center"><?= Html::encode($this->title) ?></h1>
            <p>
                Если у вас возникли вопросы или предложения по работе нашего сайта, а так же если вы хотите, чтобы ваша недвижимость отображалась на нашем сайте - пожалуйста, напишите нам, используя форму ниже.
            </p>
            <div class="row">
                <div class="col-xs-12">
                    <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                    <?= $form->field($model, 'name')->textInput(['autofocus' => true])->label("Представьтесь") ?>

                    <?= $form->field($model, 'email') ?>

                    <?= $form->field($model, 'subject')->label("Тема обращения") ?>

                    <?= $form->field($model, 'body')->textarea(['rows' => 6])->label("Сообщение") ?>

                    <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                    ])->label("Код подтверждения") ?>

                    <div class="form-group">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

        </div>
        </div>
    </div>
</div>
