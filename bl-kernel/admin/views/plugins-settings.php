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

<?php echo Bootstrap::formOpen(array('name' => 'pluginSettings')); ?>

<div class="d-flex align-items-center mb-3">
	<h2 class="m-0"><i class="bi bi-node-plus"></i><?php echo $plugin->name() ?></h2>
	<?php if ($plugin->formButtons()) : ?>
		<div class="ms-auto">
			<button type="submit" class="btn btn-primary btn-sm" name="save"><?php $L->p('Save') ?></button>
			<a class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'plugins' ?>" role="button"><?php $L->p('Cancel') ?></a>
		</div>
	<?php endif; ?>
</div>

<?php
	echo Bootstrap::formInputHidden(array(
		'name' => 'tokenCSRF',
		'value' => $security->getTokenCSRF()
	));

	if ($plugin->description()) {
		echo '<div class="alert alert-primary" role="alert">'.$plugin->description().'</div>';
	}

	echo $plugin->form();
?>

<?php echo Bootstrap::formClose(); ?>