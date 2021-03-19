<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	function configurePlugin(className) {
		var args = {
			className: className
		};

		$('input').each(function() {
			var key = $(this).attr('name');
			var value = $(this).val();
			args[key] = value;
		});

		$('select').each(function() {
			var key = $(this).attr('name');
			var value = $(this).val();
			args[key] = value;
		});

		api.configurePlugin(args).then(function(response) {
			if (response.status == 0) {
				logs('Plugin configured: ' + response.data.key);
				showAlertInfo("<?php $L->p('The changes have been saved') ?>");
			} else {
				logs('An error occurred while trying to configured the plugin.');
				showAlertError(response.message);
			}
		});
	}

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {
		$('#btnSave').on('click', function() {
			var className = $(this).data('class-name');
			configurePlugin(className);
		});
	});

	// ============================================================================
	// Initlization for the view
	// ============================================================================
	$(document).ready(function() {
		// No initialization for the view yet
	});
</script>

<div class="d-flex align-items-center mb-4">
	<h2 class="m-0"><i class="bi bi-node-plus"></i><?php echo $plugin->name() ?></h2>
	<?php if ($plugin->formButtons()) : ?>
		<div class="ms-auto">
			<button id="btnSave" type="button" class="btn btn-primary btn-sm" data-class-name="<?php echo $plugin->className() ?>"><?php $L->p('Save') ?></button>
			<a id="btnCancel" class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'plugins' ?>" role="button"><?php $L->p('Cancel') ?></a>
		</div>
	<?php endif; ?>
</div>

<?php
	if ($plugin->description()) {
		echo '<div class="alert alert-primary" role="alert">'.$plugin->description().'</div>';
	}

	echo $plugin->form();
?>
