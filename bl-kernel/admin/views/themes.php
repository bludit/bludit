<?php

echo Bootstrap::pageTitle(array('title' => $L->g('Themes'), 'icon' => 'desktop'));

echo '
<table class="table  mt-3">
	<thead>
		<tr>
			<th class="border-bottom-0 w-25" scope="col">' . $L->g('Name') . '</th>
			<th class="border-bottom-0 d-none d-sm-table-cell" scope="col">' . $L->g('Description') . '</th>
			<th class="text-center border-bottom-0 d-none d-lg-table-cell" scope="col">' . $L->g('Version') . '</th>
			<th class="text-center border-bottom-0 d-none d-lg-table-cell" scope="col">' . $L->g('Author') . '</th>
		</tr>
	</thead>
	<tbody>
';

foreach ($themes as $theme) {
  echo '
	<tr ' . ($theme['dirname'] == $site->theme() ? 'class="bg-light"' : '') . '>
		<td class="align-middle pt-3 pb-3">
		<div>'.$theme['name'].($theme['dirname']==$site->theme()?'<span class="badge badge-primary ml-2">'.$L->g('Active').'</span>':'').'</div>
			<div class="mt-1">
	';

  if ($theme['dirname'] != $site->theme()) {
    echo '<a href="' . HTML_PATH_ADMIN_ROOT . 'install-theme/' . $theme['dirname'] . '">' . $L->g('Activate') . '</a>';
  } else {
    if (isset($theme['plugin'])) {
      echo '<a href="' . HTML_PATH_ADMIN_ROOT . 'configure-plugin/' . $theme['plugin'] . '">' . $L->g('Settings') . '</a>';
    }
  }

  echo '
			</div>
		</td>
	';

  echo '<td class="align-middle d-none d-sm-table-cell">';
  echo $theme['description'];
  echo '</td>';

  echo '<td class="text-center align-middle d-none d-lg-table-cell">';
  echo '<span>' . $theme['version'] . '</span>';
  echo '</td>';

  echo '<td class="text-center align-middle d-none d-lg-table-cell">
		<a target="_blank" href="' . $theme['website'] . '">' . $theme['author'] . '</a>
	</td>';

  echo '</tr>';
}

echo '
	</tbody>
</table>
';
