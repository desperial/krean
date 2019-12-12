<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm; 
use frontend\models\SignupForm;
use yii\widgets\Pjax;
$model = new SignupForm();
Pjax::begin(['id' => 'form-signup', 'enablePushState' => false, 'scrollTo' => true]);  

    $form = ActiveForm::begin([
    	'action' => ['signup'],
	    'id' => 'form-signup',
	    'method' => 'post',
	    'options' => [
	    	'data' => ['pjax' => true],
	    	'class' => 'form-horizontal'
    	],
    	'fieldConfig' => [
	        'options' => [
	            'tag' => false,
	        ],
	    ],
	]);
?>

	<fieldset>
		<legend>Ваш электронный адрес</legend>
		<?=$form->field($model, 'email', ["template" => "{input}\n{error}"])->textInput(['placeholder' => 'example@mail.com', 'autofocus' => true, 'class' => 'form-control']) ; ?>
		<p class="help-block">Используется для входа</p>
	</fieldset>
	<fieldset>
		<legend>Пароль для входа</legend>
		<?=$form->field($model, 'password', ["template" => "{input}\n{error}"])->passwordInput(['class' => 'form-control']) ; ?>
	</fieldset>
	<fieldset>
		<legend>Ваше имя</legend>
		<?=$form->field($model, 'first_name', ["template" => "{input}\n{error}"])->textInput(['class' => 'form-control']) ; ?>
	</fieldset>
	<fieldset>
		<legend>Контактный электронный адрес</legend>
		<?=$form->field($model, 'contact_email', ["template" => "{input}\n{error}"])->textInput(['class' => 'form-control']) ; ?>
		<p class="help-block">Будет отображаться в ваших объявлениях</p>
	</fieldset>
	<fieldset>
		<legend>Контактный номер телефона</legend>
		<?=$form->field($model, 'contact_phone', ["template" => "{input}\n{error}"])->textInput(['class' => 'form-control']) ; ?>
		<p class="help-block">Будет отображаться в ваших объявлениях</p>
	</fieldset>
		<div style="float:right"><?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?></div>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
