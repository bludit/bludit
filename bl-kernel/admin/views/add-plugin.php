<?php defined('BLUDIT') or die('Bludit CMS.');

echo Bootstrap::pageTitle(array('title' => $L->g('Add a plugin'), 'icon' => 'puzzle-piece'));

echo Bootstrap::link(array(
	'title' => $L->g('Plugins'),
	'href' => HTML_PATH_ADMIN_ROOT . 'plugins',
	'icon' => 'arrow-left'
));

// The plugins are installed inside bl-plugins, without write permissions
// nothing can be installed, tell the user before he tries
if (!PluginInstaller::writable()) {
	echo Bootstrap::alert(array(
		'class' => 'alert-danger mt-3',
		'text' => '<b>' . $L->g('The directory bl-plugins is not writable') . '</b><br>' . $L->g('Change the permissions of the directory to install plugins from the admin panel')
	));
}

// ============================================================================
// Upload a plugin
// ============================================================================

echo Bootstrap::formTitle(array('title' => $L->g('Upload a plugin')));

echo '<p class="text-muted">' . $L->g('Select the zip file of the plugin, the plugin is verified before being installed') . '</p>';

echo Bootstrap::formOpen(array('method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'mb-5'));
echo Bootstrap::formInputHidden(array('name' => 'tokenCSRF', 'value' => $security->getTokenCSRF()));
echo Bootstrap::formInputHidden(array('name' => 'action', 'value' => 'upload'));
echo Bootstrap::formInputFile(array('name' => 'pluginZip', 'label' => $L->g('Zip file')));
echo '<button type="submit" class="btn btn-primary mt-3">' . $L->g('Install') . '</button>';
echo Bootstrap::formClose();

// ============================================================================
// Plugins directory
// ============================================================================

echo Bootstrap::formTitle(array('title' => $L->g('Plugins directory')));

// The directory is not available, the user is still able to upload a zip file
if ($pluginsDirectory === false) {
	echo Bootstrap::alert(array(
		'class' => 'alert-warning',
		'text' => $L->g('The plugins directory is not available, check the connection of your server to the internet')
	));

	echo Bootstrap::formOpen(array('method' => 'post'));
	echo Bootstrap::formInputHidden(array('name' => 'tokenCSRF', 'value' => $security->getTokenCSRF()));
	echo Bootstrap::formInputHidden(array('name' => 'action', 'value' => 'refresh'));
	echo '<button type="submit" class="btn btn-secondary">' . $L->g('Try again') . '</button>';
	echo Bootstrap::formClose();

	return;
}

if (empty($pluginsDirectory)) {
	echo Bootstrap::alert(array(
		'class' => 'alert-secondary',
		'text' => $L->g('There are no plugins available for this version of Bludit')
	));
	return;
}

?>

<input type="text" dir="auto" class="form-control mb-3" id="search" placeholder="<?php $L->p('Search') ?>">
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

echo '
<table class="table">
	<tbody>
';

foreach ($pluginsDirectory as $pluginId => $plugin) {
	$installed = PluginInstaller::exists($pluginId);

	echo '<tr id="' . $pluginId . '" class="searchItem">';

	echo '<td class="align-middle w-25">
		<div class="searchText">' . Sanitize::html($plugin['name']) . '</div>
		<div class="mt-1">';

	if ($installed) {
		echo '<span class="text-muted">' . $L->g('Installed') . '</span>';
	} else {
		// Plain form, the helpers Bootstrap::formOpen/formClose are not used here
		// because they add an id to every field and a script on every form
		echo '<form method="post" class="d-inline">';
		echo '<input type="hidden" name="tokenCSRF" value="' . $security->getTokenCSRF() . '">';
		echo '<input type="hidden" name="action" value="install">';
		echo '<input type="hidden" name="pluginId" value="' . $pluginId . '">';
		echo '<button type="submit" class="btn btn-link p-0 border-0 align-baseline">' . $L->g('Install') . '</button>';
		echo '</form>';
	}

	echo '</div>';
	echo '</td>';

	echo '<td class="searchText align-middle d-none d-sm-table-cell">';
	echo Sanitize::html($plugin['description']);
	echo '</td>';

	echo '<td class="text-center align-middle d-none d-lg-table-cell">';
	echo '<span>' . Sanitize::html($plugin['version']) . '</span>';
	echo '</td>';

	echo '<td class="text-center align-middle d-none d-lg-table-cell">';
	if (!empty($plugin['website'])) {
		echo '<a target="_blank" rel="noopener noreferrer" href="' . Sanitize::html($plugin['website']) . '">' . Sanitize::html($plugin['author']) . '</a>';
	} else {
		echo Sanitize::html($plugin['author']);
	}
	echo '</td>';

	echo '</tr>';
}

echo '
	</tbody>
</table>
';
