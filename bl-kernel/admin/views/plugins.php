<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Plugins'), 'icon'=>'puzzle-piece'));

echo Bootstrap::link(array(
	'title'=>$L->g('Change the position of the plugins'),
	'href'=>HTML_PATH_ADMIN_ROOT.'plugins-position',
	'icon'=>'elevator'
));

echo '
<table class="table  mt-3">
	<thead>
		<tr>
			<th class="border-bottom-0 w-25" scope="col">'.$L->g('Name').'</th>
			<th class="border-bottom-0 d-none d-sm-table-cell" scope="col">'.$L->g('Description').'</th>
			<th class="text-center border-bottom-0 d-none d-lg-table-cell" scope="col">'.$L->g('Version').'</th>
			<th class="text-center border-bottom-0 d-none d-lg-table-cell" scope="col">'.$L->g('Author').'</th>
		</tr>
	</thead>
	<tbody>
';

foreach ($plugins['all'] as $plugin) {
	echo '<tr id="'.$plugin->className().'" '.($plugin->installed()?'class="bg-light"':'').'>

	<td class="align-middle pt-3 pb-3">
		<div>'.$plugin->name().'</div>
		<div class="mt-1">';

		if ($plugin->installed()) {
			if (method_exists($plugin, 'form')) {
				echo '<a class="mr-3" href="'.HTML_PATH_ADMIN_ROOT.'configure-plugin/'.$plugin->className().'">'.$L->g('Settings').'</a>';
			}
			echo '<a href="'.HTML_PATH_ADMIN_ROOT.'uninstall-plugin/'.$plugin->className().'">'.$L->g('Deactivate').'</a>';
		} else {
			echo '<a href="'.HTML_PATH_ADMIN_ROOT.'install-plugin/'.$plugin->className().'">'.$L->g('Activate').'</a>';
		}

		echo '</div>';
	echo '</td>';

	echo '<td class="align-middle d-none d-sm-table-cell">';
		echo $plugin->description();
	echo '</td>';

	echo '<td class="text-center align-middle d-none d-lg-table-cell">';
		echo '<span>'.$plugin->version().'</span>';
	echo '</td>';

	echo '<td class="text-center align-middle d-none d-lg-table-cell">
		<a target="_blank" href="'.$plugin->website().'">'.$plugin->author().'</a>
	</td>';

	echo '</tr>';
}

echo '
	</tbody>
</table>
';