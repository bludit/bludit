<?php

echo Bootstrap::pageTitle(array('title' => $L->g('Plugins'), 'icon' => 'puzzle-piece'));

echo Bootstrap::link(array(
	'title' => $L->g('Change the position of the plugins'),
	'href' => HTML_PATH_ADMIN_ROOT . 'plugins-position',
	'icon' => 'arrows'
));

echo Bootstrap::formTitle(array('title' => $L->g('Search plugins')));

?>

<input type="text" class="form-control" id="search" placeholder="<?php $L->p('Search') ?>">
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

	echo '<td class="align-middle pt-3 pb-3 w-25">
		<div class="searchText">' . $plugin->name() . '</div>
		<div class="mt-1">';
	if (method_exists($plugin, 'form')) {
		echo '<a class="mr-3" href="' . HTML_PATH_ADMIN_ROOT . 'configure-plugin/' . $plugin->className() . '">' . $L->g('Settings') . '</a>';
	}
	echo '<a href="' . HTML_PATH_ADMIN_ROOT . 'uninstall-plugin/' . $plugin->className() . '">' . $L->g('Deactivate') . '</a>';
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

	echo '<td class="align-middle pt-3 pb-3 w-25">
		<div class="searchText">' . $plugin->name() . '</div>
		<div class="mt-1">
			<a href="' . HTML_PATH_ADMIN_ROOT . 'install-plugin/' . $plugin->className() . '">' . $L->g('Activate') . '</a>
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
