<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================
function updateBludit() {
	global $site;
	global $syslog;

	// New installation
	if ($site->currentBuild()==0) {
		$site->set(array('currentBuild'=>BLUDIT_BUILD));
	}

	// Check if Bludit need to be update
	if ( ($site->currentBuild() < BLUDIT_BUILD) || isset($_GET['update']) ) {
		Log::set('UPDATE SYSTEM - Starting.');

		// Updates only for version less than Bludit v3.0 rc-3
		if ($site->currentBuild()<='20180910') {
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

		// Updates only for version less than Bludit v3.1
		if ($site->currentBuild()<='20180921') {
			@mkdir(PATH_UPLOADS_PAGES, DIR_PERMISSIONS, true);
			$site->set(array('imageRelativeToAbsolute'=>true, 'imageRestrict'=>false));
		}

		// Set the current build number
		$site->set(array('currentBuild'=>BLUDIT_BUILD));
		Log::set('UPDATE SYSTEM - Finished.');

		// Add to syslog
		$syslog->add(array(
			'dictionaryKey'=>'system-updated',
			'notes'=>'Bludit v'.BLUDIT_VERSION
		));
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