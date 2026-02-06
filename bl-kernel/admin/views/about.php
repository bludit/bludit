<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('About'), 'icon'=>'info-circle'));

echo '
<table class="table table-striped mt-3">
	<tbody>
';

echo '<tr>';
echo '<td>Bludit Edition</td>';
if (defined('BLUDIT_PRO')) {
	echo '<td>PRO - '.$L->g('Thanks for supporting Bludit').' <span class="fa fa-heart" style="color: #ffc107"></span></td>';
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
