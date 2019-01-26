<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::formOpen(array('id'=>'jsform', 'class'=>'tab-content')); ?>

<div class="align-middle">
	<div class="float-right mt-1">
		<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
		<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT.'tags' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
	<?php echo Bootstrap::pageTitle(array('title'=>$L->g('New tag'), 'icon'=>'tag')); ?>
</div>

<?php
	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	echo Bootstrap::formInputText(array(
		'name'=>'name',
		'label'=>$L->g('Name'),
		'value'=>isset($_POST['tag'])?$_POST['tag']:'',
		'class'=>'',
		'placeholder'=>'',
		'tip'=>''
	));
?>

<?php echo Bootstrap::formClose(); ?>
