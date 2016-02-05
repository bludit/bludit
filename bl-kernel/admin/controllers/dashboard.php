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
		// --- Update dates on posts ---
		foreach($dbPosts->db as $key=>$post)
		{
			$date = Date::format($post['date'], 'Y-m-d H:i', DB_DATE_FORMAT);
			if($date !== false) {
				$dbPosts->setPostDb($key,'date',$date);
			}
		}

		$dbPosts->save();

		// --- Update dates on pages ---
		foreach($dbPages->db as $key=>$page)
		{
			$date = Date::format($page['date'], 'Y-m-d H:i', DB_DATE_FORMAT);
			if($date !== false) {
				$dbPages->setPageDb($key,'date',$date);
			}
		}

		$dbPages->save();

		// --- Update directories ---
		$directories = array(
				PATH_POSTS,
				PATH_PAGES,
				PATH_PLUGINS_DATABASES,
				PATH_UPLOADS_PROFILES,
				PATH_UPLOADS_THUMBNAILS,
				PATH_TMP
		);

		foreach($directories as $dir)
		{
			// Check if the directory is already created.
			if(!file_exists($dir)) {
				// Create the directory recursive.
				mkdir($dir, DIR_PERMISSIONS, true);
			}
		}

		// Set and save the database.
		$Site->set(array('currentBuild'=>BLUDIT_BUILD));

		Log::set('updateBludit'.LOG_SEP.'System updated');
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
