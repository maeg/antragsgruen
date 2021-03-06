<?php
/**
 * @var VeranstaltungenController $this
 * @var bool $superadmin
 * @var Veranstaltung $model
 */

if ($superadmin) {
	$this->breadcrumbs = array(
		Yii::t('app', 'Administration') => $this->createUrl("admin/index"),
		$model->label(2)                => array('index'),
		Yii::t('app', 'Update'),
	);

	$this->menu = array(
		array('label' => "Veranstaltungen", 'url' => array('index'), "icon" => "home"),
		array('label' => $model->label() . ' ' . Yii::t('app', 'Create'), 'url' => array('create'), "icon" => "plus-sign"),
		array('label' => "Anzeigen", 'url' => $this->createUrl("site/veranstaltung", array("veranstaltung_id" => $model->yii_url)), "icon" => "eye-open"),
		array('label' => $model->label() . ' ' . Yii::t('app', 'Delete'), 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm' => 'Are you sure you want to delete this item?'), "icon" => "remove"),
		array('label' => "Durchsuchen", 'url' => array('admin'), "icon" => "th-list"),
	);
} else {
	$this->breadcrumbs = array(
		Yii::t('app', 'Administration') => $this->createUrl("admin/index"),
		"Veranstaltung"
	);
}
?>

<div class="well">

	<h1><?php echo Yii::t('app', 'Update') . ': ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>



	<?php
	$this->renderPartial('_form', array(
		'model' => $model));
	?>
</div>