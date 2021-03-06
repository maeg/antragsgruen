<?php
/**
 * @var SiteController $this
 * @var OAuthLoginForm $model
 */
$this->breadcrumbs = array(
	'Login',
);


?>
<h1>Login</h1>

<h2>Wurzelwerk-Login</h2>
<div class="well">
	<div class="content">

		<?php /** @var TbActiveForm $form */
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
			'id'                     => 'verticalForm',
			'enableAjaxValidation'   => true,
			'enableClientValidation' => true,
			'htmlOptions'            => array(
				'class'            => 'well well_first',
				'validateOnSubmit' => true,
			),
		)); ?>


		<label for="OAuthLoginForm_wurzelwerk">WurzelWerk-Account</label>
		<input class="span3" name="OAuthLoginForm[wurzelwerk]" id="OAuthLoginForm_wurzelwerk" type="text" style="margin-bottom: 0; "/><br><a href="https://www.netz.gruene.de/passwordForgotten.form" target="_blank" style="font-size: 0.8em; margin-top: -7px; display: inline-block; margin-bottom: 10px;">Wurzelwerk-Zugangsdaten vergessen?</a>
		<span class="help-block error" id="OAuthLoginForm_wurzelwerk_em_" style="display: none"></span>

		<br>

		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType' => 'submit', 'icon' => 'ok', 'label' => 'Einloggen')); ?>

		<?php $this->endWidget(); ?>

	</div>
</div>

<h2>OpenID-Login</h2>
<div class="well">
	<div class="content">
		<?php /** @var TbActiveForm $form */
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
			'id'                     => 'verticalForm',
			'enableAjaxValidation'   => true,
			'enableClientValidation' => true,
			'htmlOptions'            => array(
				'class'            => 'well well_first',
				'validateOnSubmit' => true,
			),
		)); ?>

		<label for="OAuthLoginForm_openid_identifier">OpenID-URL</label>
		<input class="span3" name="OAuthLoginForm[openid_identifier]" id="OAuthLoginForm_openid_identifier" type="text"/>
		<span class="help-block error" id="OAuthLoginForm_openid_identifier_em_" style="display: none"></span>

		<br>

		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType' => 'submit', 'icon' => 'ok', 'label' => 'Einloggen')); ?>

		<?php $this->endWidget(); ?>
	</div>
</div>


<h2>Login per Benutzername / Passwort</h2>
<div class="well">
	<div class="content">

		<?php /** @var TbActiveForm $form */
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
			'id'                     => 'verticalForm',
			'enableAjaxValidation'   => true,
			'enableClientValidation' => true,
			'htmlOptions'            => array(
				'class'            => 'well well_first',
				'validateOnSubmit' => true,
			),
		)); ?>


		<label for="OAuthLoginForm_wurzelwerk">WurzelWerk-Account</label>
		<input class="span3" name="OAuthLoginForm[wurzelwerk]" id="OAuthLoginForm_wurzelwerk" type="text"/>
		<span class="help-block error" id="OAuthLoginForm_wurzelwerk_em_" style="display: none"></span>

		<label>Passwort:<br><input type="password" value="" autocomplete="false" name="password"></label>

		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType' => 'submit', 'icon' => 'ok', 'label' => 'Einloggen')); ?>

		<?php $this->endWidget(); ?>

	</div>
</div>
