<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<?php echo Bootstrap::formOpen(array('id'=>'jsform', 'class'=>'plugin-form')); ?>

<div class="form-header">
	<?php if ($plugin->formButtons()): ?>
	<div class="form-header-actions">
		<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
		<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT.'plugins' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
	<?php endif; ?>
	<?php echo Bootstrap::pageTitle(array('title'=>$plugin->name(), 'icon'=>'puzzle-piece')); ?>
</div>

<div class="form-card">

<?php
	// Token CSRF
	echo Bootstrap::formInputHidden(array(
		'name'=>'tokenCSRF',
		'value'=>$security->getTokenCSRF()
	));

	// Compatibility warning
	if (!$plugin->isCompatible()) {
		echo '<div class="alert alert-warning mt-2">' . $L->g('This plugin may not be supported by this version of Bludit') . '</div>';
	}

	// Print the plugin form
	echo $plugin->form();
?>

</div>
<?php echo Bootstrap::formClose(); ?>