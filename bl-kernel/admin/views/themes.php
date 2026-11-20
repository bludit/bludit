<?php

echo Bootstrap::pageTitle(array('title' => $L->g('Themes'), 'icon' => 'desktop'));

echo '
<div class="table-card mt-3">
<table class="table">
	<thead>
		<tr>
			<th class="w-25" scope="col">' . $L->g('Name') . '</th>
			<th class="d-none d-sm-table-cell" scope="col">' . $L->g('Description') . '</th>
			<th class="text-center d-none d-lg-table-cell" scope="col">' . $L->g('Version') . '</th>
			<th class="text-center d-none d-lg-table-cell" scope="col">' . $L->g('Author') . '</th>
		</tr>
	</thead>
	<tbody>
';

foreach ($themes as $theme) {
  echo '
	<tr ' . ($theme['dirname'] == $site->theme() ? 'class="row-active"' : '') . '>
		<td class="align-middle">
		<div class="row-title">'.$theme['name'].($theme['dirname']==$site->theme()?'<span class="status-badge status-badge-inline status-published ms-2">'.$L->g('Active').'</span>':'').'</div>
			<div class="row-links">
	';

  if ($theme['locked']) {
    echo '<a target="_blank" href="https://pro.bludit.com"><span class="fa fa-lock"></span> ' . $L->g('Requires Bludit PRO') . '</a>';
  } elseif ($theme['dirname'] != $site->theme()) {
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

  echo '<td class="align-middle d-none d-sm-table-cell row-text">';
  echo $theme['description'];
  echo '</td>';

  echo '<td class="text-center align-middle d-none d-lg-table-cell">';
  echo '<span class="row-muted">' . $theme['version'] . '</span>';
  echo '</td>';

  echo '<td class="text-center align-middle d-none d-lg-table-cell">
		<a class="row-muted" target="_blank" href="' . $theme['website'] . '">' . $theme['author'] . '</a>
	</td>';

  echo '</tr>';
}

echo '
	</tbody>
</table>
</div>
';
