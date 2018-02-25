<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================
function updateBludit() {
	global $Site;
	// Check if Bludit need to be update.
	if( ($Site->currentBuild() < BLUDIT_BUILD) || isset($_GET['update']) ) {
		Log::set('UPDATE SYSTEM - Starting.');

		// From Bludit v2.0.x to v2.1.x
		if ($Site->currentBuild() < '20171102') {
			// Nothing to do
		}

		// Set the current build number
		$Site->set(array('currentBuild'=>BLUDIT_BUILD));
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
$layout['title'] .= ' - '.$Language->g('Dashboard');