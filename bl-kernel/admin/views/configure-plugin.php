<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::formOpen(array('id'=>'jsform', 'class'=>'plugin-form')); ?>

<div class="align-middle">
	<?php if ($plugin->formButtons()): ?>
	<div class="float-right mt-1">
		<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
		<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
	<?php endif; ?>
	<?php echo Bootstrap::pageTitle(array('title'=>$plugin->name(), 'icon'=>'cog')); ?>
</div>

<?php
	// Token CSRF
	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	// Print the plugin form
	echo $plugin->form();
?>

<?php echo Bootstrap::formClose(); ?>