<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Version'), 'icon'=>'info-circle'));

echo '
<table class="table mt-3">
	<tbody>
';

echo '<tr>';
echo '<td>Bludit Edition</td>';
if (defined('BLUDIT_PRO')) {
	echo '<td>PRO - '.$L->g('Thanks for supporting Bludit').' <span class="fa fa-heart" style="color: #ffc107"></span></td>';
} elseif (defined('BLUDIT_PRO_LICENSE_INVALID')) {
	echo '<td>Standard - '.sprintf($L->g('The Bludit PRO license is not valid for this version'), Sanitize::html(BLUDIT_PRO_LICENSE_INVALID), Sanitize::html(BLUDIT_VERSION)).'</td>';
} else {
	echo '<td>Standard - <a target="_blank" href="https://pro.bludit.com">'.$L->g('Upgrade to Bludit PRO').'</a></td>';
}
echo '</tr>';

echo '<tr>';
echo '<td>Bludit Version</td>';
echo '<td>'.BLUDIT_VERSION.'</td>';
echo '</tr>';

echo '<tr>';
echo '<td>Bludit Codename</td>';
echo '<td>'.BLUDIT_CODENAME.'</td>';
echo '</tr>';

echo '<tr>';
echo '<td>Bludit Build Number</td>';
echo '<td>'.BLUDIT_BUILD.'</td>';
echo '</tr>';

echo '<tr>';
echo '<td>Disk usage</td>';
echo '<td>'.Filesystem::bytesToHumanFileSize(Filesystem::getSize(PATH_ROOT)).'</td>';
echo '</tr>';

echo '<tr>';
echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'developers'.'">Bludit Developers</a></td>';
echo '<td></td>';
echo '</tr>';

echo '
	</tbody>
</table>
';

// Updates of Bludit, only for administrators
// The version plugin is the only thing that reaches out to bludit.com, disabling
// it keeps the site from making that connection at all
if ($login->role() === 'admin') {
	echo Bootstrap::formTitle(array('title' => $L->g('Updates')));

	if (pluginActivated('pluginVersion')) {
		echo getPlugin('pluginVersion')->renderUpdates();
	} else {
		echo '<p>' . sprintf($L->g('Enable the %s plugin to check for updates of Bludit'), $L->g('Version')) . ' <a href="' . HTML_PATH_ADMIN_ROOT . 'plugins' . '">' . $L->g('Plugins') . '</a></p>';
	}
}
