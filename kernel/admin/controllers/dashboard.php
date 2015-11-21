<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================
function updateBludit()
{
	global $Site;

	// Check if Bludit need to be update.
	if($Site->currentBuild() < BLUDIT_BUILD)
	{
		$directories = array(PATH_POSTS, PATH_PAGES, PATH_PLUGINS_DATABASES, PATH_UPLOADS_PROFILES);

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
