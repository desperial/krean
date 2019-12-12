<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm; 
use common\models\LoginForm;
$model = new LoginForm();

    $form = ActiveForm::begin([
	    'id' => 'login-form',
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
<form class="form" name="login">
	<fieldset>
		<legend>Электронный адрес</legend>
		<?=$form->field($model, 'email', ["template" => "{input}\n{error}"])->textInput(['placeholder' => 'example@mail.com', 'autofocus' => true, 'class' => 'form-control']) ; ?>
	</fieldset>
	<fieldset>
		<legend>Пароль</legend>
		<?=$form->field($model, 'password', ["template" => "{input}\n{error}"])->passwordInput(['class' => 'form-control']) ; ?>
	</fieldset>
	<fieldset>
		<div style="float:left"><?= $form->field($model, 'rememberMe')->checkbox() ?> Запомнить меня</div>
		<div style="float:right"><?= Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?></div>
		<div class="clear"></div>
	</fieldset>
</form>
<?php ActiveForm::end(); ?>