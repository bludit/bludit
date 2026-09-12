<?php

echo Bootstrap::pageTitle(array('title' => $L->g('Plugins'), 'icon' => 'puzzle-piece'));

echo Bootstrap::link(array(
	'title' => $L->g('Add a plugin'),
	'href' => HTML_PATH_ADMIN_ROOT . 'add-plugin',
	'icon' => 'plus'
));

echo Bootstrap::link(array(
	'title' => $L->g('Change the position of the plugins'),
	'href' => HTML_PATH_ADMIN_ROOT . 'plugins-position',
	'icon' => 'arrows',
	'class' => 'ms-3'
));

// Cached list of plugins from the directory, it's used to know if there is a
// new version of a plugin installed, the directory is never downloaded here
$pluginsAvailable = (PluginsDirectory::cacheAge() === false) ? array() : PluginsDirectory::getIndex();
if ($pluginsAvailable === false) {
	$pluginsAvailable = array();
}

// Returns the row of the plugin in the directory when there is a new version
function pluginUpdateAvailable($plugin, $pluginsAvailable) {
	$id = $plugin->directoryName();
	if (!isset($pluginsAvailable[$id])) {
		return false;
	}
	if (version_compare($pluginsAvailable[$id]['version'], $plugin->version(), '>')) {
		return $pluginsAvailable[$id];
	}
	return false;
}

// Buttons to update and delete a plugin, plain forms so the action is a POST
// protected by the tokenCSRF, the links to activate and deactivate are GET
function pluginActions($plugin, $pluginsAvailable) {
	global $L;
	global $security;

	$id = $plugin->directoryName();
	$html = '';

	if (pluginUpdateAvailable($plugin, $pluginsAvailable) !== false) {
		$html .= '<form method="post" class="d-inline me-3" action="' . HTML_PATH_ADMIN_ROOT . 'update-plugin">';
		$html .= '<input type="hidden" name="tokenCSRF" value="' . $security->getTokenCSRF() . '">';
		$html .= '<input type="hidden" name="pluginId" value="' . $id . '">';
		$html .= '<button type="submit" class="btn btn-link p-0 border-0 align-baseline">' . $L->g('Update') . '</button>';
		$html .= '</form>';
	}

	$html .= '<form method="post" class="d-inline" action="' . HTML_PATH_ADMIN_ROOT . 'delete-plugin' . '" onsubmit="return confirm(\'' . $L->g('Are you sure you want to delete this plugin?') . '\')">';
	$html .= '<input type="hidden" name="tokenCSRF" value="' . $security->getTokenCSRF() . '">';
	$html .= '<input type="hidden" name="pluginId" value="' . $id . '">';
	$html .= '<button type="submit" class="btn btn-link p-0 border-0 align-baseline text-danger">' . $L->g('Delete') . '</button>';
	$html .= '</form>';

	return $html;
}

echo Bootstrap::formTitle(array('title' => $L->g('Search plugins')));

?>

<input type="text" dir="auto" class="form-control" id="search" placeholder="<?php $L->p('Search') ?>">
<script>
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
	});
</script>

<?php

echo Bootstrap::formTitle(array('title' => $L->g('Enabled plugins')));

echo '
<table class="table">
	<tbody>
';

// Show installed plugins
foreach ($pluginsInstalled as $plugin) {

	if ($plugin->type() == 'theme') {
		// Do not display theme's plugins
		continue;
	}

	echo '<tr id="' . $plugin->className() . '" class="bg-light searchItem">';

	echo '<td class="align-middle w-25">
		<div class="searchText">' . $plugin->name() . '</div>
		<div class="mt-1">';
	if (method_exists($plugin, 'form')) {
		echo '<a class="me-3" href="' . HTML_PATH_ADMIN_ROOT . 'configure-plugin/' . $plugin->className() . '">' . $L->g('Settings') . '</a>';
	}
	echo '<a class="me-3" href="' . HTML_PATH_ADMIN_ROOT . 'uninstall-plugin/' . $plugin->className() . '">' . $L->g('Deactivate') . '</a>';
	echo pluginActions($plugin, $pluginsAvailable);
	echo '</div>';
	echo '</td>';

	echo '<td class="searchText align-middle d-none d-sm-table-cell">';
	echo $plugin->description();
	echo '</td>';

	echo '<td class="text-center align-middle d-none d-lg-table-cell">';
	echo '<span>' . $plugin->version() . '</span>';
	$newVersion = pluginUpdateAvailable($plugin, $pluginsAvailable);
	if ($newVersion !== false) {
		echo ' <span class="badge rounded-pill text-bg-success" title="' . $L->g('There is a new version available') . '">' . Sanitize::html($newVersion['version']) . '</span>';
	} elseif (!$plugin->isCompatible()) {
		echo ' <span class="badge rounded-pill text-bg-warning" title="' . $L->g('This plugin may not be supported by this version of Bludit') . '">' . $L->g('Update') . '</span>';
	}
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

echo Bootstrap::formTitle(array('title' => $L->g('Disabled plugins')));

echo '
<table class="table">
	<tbody>
';

// Plugins not installed
$pluginsNotInstalled = array_diff_key($plugins['all'], $pluginsInstalled);
foreach ($pluginsNotInstalled as $plugin) {

	if ($plugin->type() == 'theme') {
		// Do not display theme's plugins
		continue;
	}
	echo '<tr id="' . $plugin->className() . '" class="searchItem">';

	echo '<td class="align-middle w-25">
		<div class="searchText">' . $plugin->name() . '</div>
		<div class="mt-1">
			<a class="me-3" href="' . HTML_PATH_ADMIN_ROOT . 'install-plugin/' . $plugin->className() . '">' . $L->g('Activate') . '</a>
			' . pluginActions($plugin, $pluginsAvailable) . '
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
