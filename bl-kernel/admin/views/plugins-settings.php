<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {
		// No events for the view yet
	});

	// ============================================================================
	// Initlization for the view
	// ============================================================================
	$(document).ready(function() {
		// No initialization for the view yet
	});
</script>

<?php echo Bootstrap::formOpen(array('id'=>'jsform', 'class'=>'plugin-form')); ?>

<div class="align-middle">
	<?php if ($plugin->formButtons()): ?>
	<div class="float-end mt-1">
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