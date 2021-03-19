<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================
	function changePluginsPosition() {
		$("li.list-group-item").each(function(index, value) {
			var args = {
				position: index,
				className: $(this).data("class-name")
			};
			console.log(index);
			api.configurePlugin(args).then(function(response) {
				if (response.status == 0) {
					logs('Plugin configured: ' + response.data.key);
				} else {
					logs('An error occurred while trying to configured the plugin.');
					showAlertError(response.message);
				}
			});
		});

		showAlertInfo("<?php $L->p('The changes have been saved') ?>");
	}

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {
		$("#btnSave").on("click", function() {
			changePluginsPosition();
		});
	});

	// ============================================================================
	// Initlization for the view
	// ============================================================================
	$(document).ready(function() {
		$('.list-group-sortable').sortable({
			placeholderClass: 'list-group-item'
		});
	});
</script>

<div class="d-flex align-items-center mb-4">
	<h2 class="m-0"><i class="bi bi-arrow-down-up"></i><?php $L->p('Plugins position') ?></h2>
	<div class="ms-auto">
		<button id="btnSave" type="button" class="btn btn-primary btn-sm"><?php $L->p('Save') ?></button>
		<a id="btnCancel" class="btn btn-secondary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'plugins' ?>" role="button"><?php $L->p('Cancel') ?></a>
	</div>
</div>

<div class="alert alert-primary">
	<?php $L->p('Drag and Drop to sort the plugins') ?>
</div>

<?php echo Bootstrap::formTitle(array('title' => $L->g('Website plugins'))) ?>

<ul class="website-plugins list-group list-group-sortable">
<?php foreach ($plugins['siteSidebar'] as $plugin): ?>
	<li class="list-group-item" data-class-name="<?php echo $plugin->className() ?>">
		<i class="bi bi-arrows-expand"></i><?php echo $plugin->name() ?>
	</li>
<?php endforeach; ?>
</ul>

<?php echo Bootstrap::formTitle(array('title' => $L->g('Dashboard plugins'))) ?>

<ul class="dashboard-plugins list-group list-group-sortable">
<?php foreach ($plugins['dashboard'] as $plugin): ?>
	<li class="list-group-item" data-class-name="<?php echo $plugin->className() ?>">
		<i class="bi bi-arrows-expand"></i><?php echo $plugin->name() ?>
	</li>
<?php endforeach; ?>
</ul>