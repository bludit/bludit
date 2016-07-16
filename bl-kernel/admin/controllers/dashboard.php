<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================
function updateBludit()
{
	global $Site;
	global $dbPosts;
	global $dbPages;

	// Check if Bludit need to be update.
	if( ($Site->currentBuild() < BLUDIT_BUILD) || isset($_GET['update']) )
	{
		// LOG
		Log::set('UPDATE SYSTEM - Starting...');

		// LOG
		Log::set('UPDATE SYSTEM - Checking posts.');

		// Update posts
		foreach($dbPosts->db as $key=>$post) {

			// Dates
			$date = Date::format($post['date'], 'Y-m-d H:i', DB_DATE_FORMAT);
			if($date !== false) {
				$dbPosts->setPostDb($key, 'date', $date);
			}

			// Checksum
			if( empty($post['md5file']) ) {
				$checksum = md5_file(PATH_POSTS.$key.DS.FILENAME);
				$dbPosts->setPostDb($key, 'md5file', $checksum);
			}
		}

		$dbPosts->save();

		// LOG
		Log::set('UPDATE SYSTEM - Checking pages.');

		// Update pages
		foreach($dbPages->db as $key=>$page) {

			$date = Date::format($page['date'], 'Y-m-d H:i', DB_DATE_FORMAT);
			if($date !== false) {
				$dbPages->setPageDb($key, 'date', $date);
			}

			// Checksum
			if( empty($post['md5file']) ) {
				$checksum = md5_file(PATH_PAGES.$key.DS.FILENAME);
				$dbPages->setPageDb($key, 'md5file', $checksum);
			}
		}

		$dbPages->save();

		// LOG
		Log::set('UPDATE SYSTEM - Checking directories.');

		// --- Update directories ---
		$directories = array(
				PATH_POSTS,
				PATH_PAGES,
				PATH_PLUGINS_DATABASES,
				PATH_UPLOADS_PROFILES,
				PATH_UPLOADS_THUMBNAILS,
				PATH_TMP
		);

		foreach($directories as $dir) {

			// Check if the directory is already created.
			if(!file_exists($dir)) {
				// Create the directory recursive.
				mkdir($dir, DIR_PERMISSIONS, true);
			}
		}

		// Set and save the database.
		$Site->set(array('currentBuild'=>BLUDIT_BUILD));

		// LOG
		Log::set('UPDATE SYSTEM - Updated...');
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

// Get draft posts and schedules
$_draftPosts = array();
$_scheduledPosts = array();
foreach($posts as $Post)
{
	if($Post->draft()) {
		array_push($_draftPosts, $Post);
	}
	elseif($Post->scheduled()) {
		array_push($_scheduledPosts, $Post);
	}
}

// Get draft pages
$_draftPages = array();
foreach($pages as $Page)
{
	if(!$Page->published()) {
		array_push($_draftPages, $Page);
	}
}
