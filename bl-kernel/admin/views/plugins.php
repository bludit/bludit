<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	function activatePlugin(pluginClassName) {
		var args = {
			pluginClassName: pluginClassName
		};
		api.activatePlugin(args).then(function(response) {
			if (response.status == 0) {
				logs('Plugin activated: ' + response.data.key);
				window.location.replace('<?php echo HTML_PATH_ADMIN_ROOT . 'plugins-settings/' ?>'+response.data.key);
			} else {
				logs('An error occurred while trying to activate the plugin.');
				showAlertError(response.message);
			}
		});
	}

	function deactivatePlugin(pluginClassName) {
		var args = {
			pluginClassName: pluginClassName
		};
		api.deactivatePlugin(args).then(function(response) {
			if (response.status == 0) {
				logs('Plugin deactivated: ' + response.data.key);
				window.location.replace('<?php echo HTML_PATH_ADMIN_ROOT . 'plugins' ?>');
			} else {
				logs('An error occurred while trying to deactivate the plugin.');
				showAlertError(response.message);
			}
		});
	}

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {

		$("#search").on("keyup", function() {
			var textToSearch = $(this).val().toLowerCase();
			$(".searchItem").each(function() {
				var item = $(this);
				item.hide();
				item.find(".searchText").each(function() {
					var element = $(this).text().toLowerCase();
					if (element.indexOf(textToSearch) != -1) {
						item.show();
					}
				});
			});
		});

		$('.activatePlugin').on('click', function() {
			var pluginClassName = $(this).data('class-name');
			activatePlugin(pluginClassName);
		});

		$('.deactivatePlugin').on('click', function() {
			var pluginClassName = $(this).data('class-name');
			deactivatePlugin(pluginClassName);
		});

	});

	// ============================================================================
	// Initialization for the view
	// ============================================================================
	$(document).ready(function() {
		// nothing here yet
		// how do you hang your toilet paper ? over or under ?
	});
</script>

<div class="d-flex align-items-center mb-4">
	<h2 class="m-0"><i class="bi bi-node-plus"></i><?php $L->p('Plugins') ?></h2>
	<div class="ms-auto">
		<a id="btnNew" class="btn btn-primary btn-sm" href="<?php echo HTML_PATH_ADMIN_ROOT . 'plugins-position' ?>" role="button"><i class="bi bi-plus-circle"></i><?php $L->p('Change plugins position') ?></a>
	</div>
</div>

<?php echo Bootstrap::formTitle(array('icon' => 'search', 'title' => $L->g('Search plugins'))); ?>

<input type="text" class="form-control" id="search" placeholder="<?php $L->p('Search') ?>">

<?php

echo Bootstrap::formTitle(array('icon' => 'check-square', 'title' => $L->g('Enabled plugins')));

echo '
<table class="table table-striped">
	<tbody>
';

// Show installed plugins
foreach ($pluginsInstalled as $plugin) {
	echo '<tr id="' . $plugin->className() . '" class="bg-light searchItem">';

	echo '<td class="align-middle pt-3 pb-3 w-25">
		<div class="searchText">' . $plugin->name() . '</div>
		<div class="mt-1">';
	if (method_exists($plugin, 'form')) {
		echo '<a class="me-3" href="' . HTML_PATH_ADMIN_ROOT . 'plugins-settings/' . $plugin->className() . '">' . $L->g('Settings') . '</a>';
	}
	echo '<span class="link deactivatePlugin" data-class-name="' . $plugin->className() . '">' . $L->g('Deactivate') . '</a>';
	echo '</div>';
	echo '</td>';

	echo '<td class="searchText align-middle d-none d-sm-table-cell">';
	echo $plugin->description();
	echo '</td>';

	echo '<td class="text-center align-middle d-none d-lg-table-cell">';
	echo '<span>' . $plugin->version() . '</span>';
	echo '</td>';

	echo '<td class="text-center align-middle d-none d-lg-table-cell">
		<a target="_blank" href="' . $plugin->website() . '">' . $plugin->author() . '</a>
	</td>';

	echo '</tr>';
}

echo '
	</tbody>
</table>
';

echo Bootstrap::formTitle(array('icon' => 'dash-square', 'title' => $L->g('Disabled plugins')));

echo '
<table class="table table-striped">
	<tbody>
';

// Plugins not installed
$pluginsNotInstalled = array_diff_key($plugins['all'], $pluginsInstalled);
foreach ($pluginsNotInstalled as $plugin) {
	echo '<tr id="' . $plugin->className() . '" class="searchItem">';

	echo '<td class="align-middle pt-3 pb-3 w-25">
		<div class="searchText">' . $plugin->name() . '</div>
		<div class="mt-1">
			<span class="link activatePlugin" data-class-name="' . $plugin->className() . '">' . $L->g('Activate') . '</a>
		</div>
	</td>';

	echo '<td class="searchText align-middle d-none d-sm-table-cell">';
	echo $plugin->description();
	echo '</td>';

	echo '<td class="text-center align-middle d-none d-lg-table-cell">';
	echo '<span>' . $plugin->version() . '</span>';
	echo '</td>';

	echo '<td class="text-center align-middle d-none d-lg-table-cell">
		<a target="_blank" href="' . $plugin->website() . '">' . $plugin->author() . '</a>
	</td>';

	echo '</tr>';
}

echo '
	</tbody>
</table>
';
