<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::formOpen(array('id'=>'jsform', 'class'=>'tab-content')); ?>

<div class="form-header">
	<div class="form-header-actions">
		<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
		<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT.'categories' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
	<?php echo Bootstrap::pageTitle(array('title'=>$L->g('New category'), 'icon'=>'tags')); ?>
</div>

<div class="form-card">

<?php
	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	echo Bootstrap::formInputText(array(
		'name'=>'name',
		'label'=>$L->g('Name'),
		'value'=>isset($_POST['category'])?$_POST['category']:'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));

	echo Bootstrap::formTextarea(array(
		'name'=>'description',
		'label'=>$L->g('Description'),
		'value'=>isset($_POST['description'])?$_POST['description']:'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>'',
		'rows'=>3
	));
?>

</div>
<?php echo Bootstrap::formClose(); ?>
