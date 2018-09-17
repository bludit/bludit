<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================
function updateBludit() {
	global $site;
	// Check if Bludit need to be update.
	if( ($site->currentBuild() < BLUDIT_BUILD) || isset($_GET['update']) ) {
		Log::set('UPDATE SYSTEM - Starting.');

		// Updates only for version less than Bludit v3.0 rc-3
		if ($site->currentBuild()<'20180910') {
			@mkdir(PATH_WORKSPACES, DIR_PERMISSIONS, true);
			$plugins = array('simple-stats', 'pluginRSS', 'pluginSitemap', 'pluginTimeMachineX', 'pluginBackup');
			foreach ($plugins as $plugin) {
				if (pluginActivated($plugin)) {
					Log::set('UPDATE SYSTEM - Re-enable plugin: '.$plugin);
					deactivatePlugin($plugin);
					activatePlugin($plugin);
				}
			}
		}

		// Set the current build number
		$site->set(array('currentBuild'=>BLUDIT_BUILD));
		Log::set('UPDATE SYSTEM - Finished.');
	}
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

// ============================================================================
// Main after POST
// ============================================================================

// Try update Bludit
updateBludit();

// Title of the page
$layout['title'] .= ' - '.$L->g('Dashboard');