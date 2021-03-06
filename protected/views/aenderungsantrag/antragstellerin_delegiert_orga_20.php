<?php

/**
 * @var AenderungsantragController $this
 * @var string $mode
 * @var Antrag $antrag
 * @var Aenderungsantrag $aenderungsantrag
 * @var array $hiddens
 * @var bool $js_protection
 * @var Sprache $sprache
 * @var Person $antragstellerin
 */


if ($mode == "neu") {
	/** @var Person $antragstellerin */
	?>
	<div class="well">
		<h3><?=$sprache->get("AntragstellerIn")?></h3>
		<br>

		<?php echo $form->textFieldRow($antragstellerin, 'name'); ?>

		<?php echo $form->textFieldRow($antragstellerin, 'email', array("required" => true)); ?>

		<?php echo $form->textFieldRow($antragstellerin, 'telefon'); ?>

		<div class="control-group" id="Person_typ_chooser">
			<label class="control-label">Ich bin...</label>
			<div class="controls">
				<label><input type="radio" name="Person[typ]" value="delegiert" required /> einE DelegierteR</label>
				<label><input type="radio" name="Person[typ]" value="mitglied" required /> Parteimitglied (nicht delegiert)</label>
				<label><input type="radio" name="Person[typ]" value="organisation" required /> ein Gremium, LAK, ...</label>
			</div>
		</div>

		<div class="control-group" id="UnterstuetzerInnen">
			<label class="control-label">UnterstützerInnen<br>(min. 19)</label>
			<div class="controls">
				<?php for ($i = 0; $i < 19; $i++) { ?>
				<input type="text" name="UnterstuetzerInnen[]" value="" placeholder="Name" title="Name der UnterstützerInnen"><br>
				<?php } ?>
			</div>
		</div>

		<script>
			$(function() {
				var $chooser = $("#Person_typ_chooser");
				var $unter = $("#UnterstuetzerInnen");
				$chooser.find("input").change(function() {
					if ($chooser.find("input:checked").val() == "mitglied") {
						$unter.show();
						$unter.find("input[type=text]").prop("required", true);
					} else {
						$unter.hide();
						$unter.find("input[type=text]").prop("required", false);
					}
				}).change();
			})
		</script>

		<div class="ae_select_confirm">
			<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=> 'submit', 'type'=> 'primary', 'icon'=> 'ok white', 'label'=> $sprache->get("Änderungsantrag stellen"))); ?>
		</div>

		<br><br>

	</div>
<?php }
