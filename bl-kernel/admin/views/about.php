<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('About'), 'icon'=>'info-circle'));

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

// Updates of Bludit, only for administrators, core.json is cached so the page
// downloads it at most once every CORE_UPDATE_CACHE_TTL
if ($login->role() === 'admin') {
	echo Bootstrap::formTitle(array('title' => $L->g('Updates')));

	$release = CoreUpdater::getRelease();
	if ($release === false) {
		echo '<p>' . $L->g('Unable to check the updates of Bludit') . '</p>';
	} elseif (!CoreUpdater::available($release)) {
		echo '<p>' . $L->g('You have the last version of Bludit') . '</p>';
	} else {
		$newVersion = Sanitize::html($release['version']);
		$download = CoreUpdater::allowedURL($release['download']) ? Sanitize::html($release['download']) : '';
		$notWritable = CoreUpdater::notWritable();

		echo '<p>' . sprintf($L->g('Bludit %s is available'), $newVersion) . '</p>';

		if (!CoreUpdater::phpCompatible($release)) {
			echo '<div class="alert alert-warning">' . sprintf($L->g('The new version of Bludit requires PHP %s'), Sanitize::html($release['php'])) . '</div>';
		} elseif (!empty($notWritable)) {
			// The update is not possible, the files are replaced by hand
			echo '<div class="alert alert-warning">' . $L->g('Bludit can not write in these directories') . ' <code>' . Sanitize::html(implode(', ', $notWritable)) . '</code>. ' . $L->g('Update Bludit manually replacing the files') . '</div>';
			if ($download !== '') {
				echo '<a class="btn btn-primary" href="' . $download . '">' . $L->g('Download') . ' Bludit ' . $newVersion . '</a>';
			}
		} else {
			if (defined('BLUDIT_PRO') && CoreUpdater::changesMinorVersion($release)) {
				echo '<div class="alert alert-warning">' . sprintf($L->g('The Bludit PRO license is for this version, after the update download the license for Bludit %s from Patreon'), $newVersion) . '</div>';
			}
			echo '<p class="text-muted">' . $L->g('The content and the settings are not modified, the previous version is restored if the update fails') . '</p>';
			$confirmation = Sanitize::html(addslashes(sprintf($L->g('Update Bludit to the version %s?'), $release['version'])));
			echo '<form method="post" action="' . HTML_PATH_ADMIN_ROOT . 'update-bludit" onsubmit="return confirm(\'' . $confirmation . '\')">';
			echo '<input type="hidden" name="tokenCSRF" value="' . $security->getTokenCSRF() . '">';
			echo '<button type="submit" class="btn btn-primary">' . $L->g('Update') . ' Bludit ' . $newVersion . '</button>';
			echo '</form>';
		}
	}
}
