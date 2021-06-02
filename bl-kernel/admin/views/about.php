<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {
		// No events for the view yet
	});

	// ============================================================================
	// Initialization for the view
	// ============================================================================
	$(document).ready(function() {
		// No initialization for the view yet
	});
</script>

<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('About'), 'icon'=>'info-circle'));

echo '
<table class="table table-striped mt-3">
	<tbody>
';

echo '<tr>';
echo '<td class="pt-4 pb-4">Bludit Edition</td>';
if (defined('BLUDIT_PRO')) {
	echo '<td class="pt-4 pb-4">PRO - '.$L->g('Thanks for supporting Bludit').' <span class="bi-heart" style="color: #ffc107"></span></td>';
} else {
	echo '<td class="pt-4 pb-4">Standard - <a target="_blank" href="https://pro.bludit.com">'.$L->g('Upgrade to Bludit PRO').'</a></td>';
}
echo '</tr>';

echo '<tr>';
echo '<td class="pt-4 pb-4">Bludit Version</td>';
echo '<td class="pt-4 pb-4">'.BLUDIT_VERSION.'</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="pt-4 pb-4">Bludit Codename</td>';
echo '<td class="pt-4 pb-4">'.BLUDIT_CODENAME.'</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="pt-4 pb-4">Bludit Build Number</td>';
echo '<td class="pt-4 pb-4">'.BLUDIT_BUILD.'</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="pt-4 pb-4">Disk usage</td>';
echo '<td class="pt-4 pb-4">'.Filesystem::bytesToHumanFileSize(Filesystem::getSize(PATH_ROOT)).'</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="pt-4 pb-4"><a href="'.HTML_PATH_ADMIN_ROOT.'developers'.'">Bludit Developers</a></td>';
echo '<td class="pt-4 pb-4"></td>';
echo '</tr>';

echo '
	</tbody>
</table>
';
