<?php
/* @var $this AenderungsantragController */
/* @var $data Aenderungsantrag */
/* @var $model Aenderungsantrag */
?>

<table class="view">

        <tr>
            <th colspan="2"><?php echo GxHtml::link(GxHtml::encode("ID " . $data->id . ": " . $data->revision_name), array('update', 'id' => $data->id)); ?></th>
        </tr>
        <tr>
            <th><?php echo Yii::t("app", "Zu"); ?>:</th>
            <td><?php echo GxHtml::encode(GxHtml::encode($data->antrag->revision_name . ": " . $data->antrag->name)); ?></td>
        </tr>
    <tr>
        <th><?php echo GxHtml::encode($data->getAttributeLabel('status')); ?>:</th>
        <td><?php
            echo GxHtml::encode(Aenderungsantrag::$STATI[$data->status]);
            if ($data->status_string != "") echo GxHtml::encode($data->status_string);
            ?></td>
    </tr>
	<tr>
		<th>AntragstellerIn:</th>
		<td><?php
			$x = array();
			foreach ($data->aenderungsantragUnterstuetzer as $unt) {
				if (in_array($unt->rolle, array(AenderungsantragUnterstuetzer::$ROLLE_INITIATOR, AenderungsantragUnterstuetzer::$ROLLE_UNTERSTUETZER))) {
					if ($unt->unterstuetzer->email != "") $x[] = CHtml::encode($unt->unterstuetzer->name . " (" . $unt->unterstuetzer->email . ")");
					else $x[] = CHtml::encode($unt->unterstuetzer->name);
				}
			}
			echo implode(", ", $x);
			?></td>
	</tr>
    <tr>
            <th><?php echo GxHtml::encode($data->getAttributeLabel('aenderung_text')); ?>:</th>
            <td><?php echo HtmlBBcodeUtils::bbcode2html($data->aenderung_text); ?></td>
        </tr>
        <tr>
            <th><?php echo GxHtml::encode($data->getAttributeLabel('datum_einreichung')); ?>:</th>
            <td><?php echo GxHtml::encode($data->datum_einreichung); ?></td>
        </tr>
        <tr>
            <th><?php echo GxHtml::encode($data->getAttributeLabel('datum_beschluss')); ?>:</th>
            <td><?php echo GxHtml::encode($data->datum_beschluss); ?></td>
        </tr>
</table>
