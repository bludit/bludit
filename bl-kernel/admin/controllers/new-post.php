<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

// ============================================================================
// Functions
// ============================================================================
function addPost($args)
{
	global $dbPosts;
	global $Language;
	global $adminfolder;
	
	// Add the page, if the $key is FALSE the creation of the post failure.
	$key = $dbPosts->add($args);

	if($key) {
		// Reindex tags, this function is in 70.posts.php
		reIndexTagsPosts();

		// Re index categories
		//reIndexCategoriesPosts();

		// Call the plugins after post creation
		Theme::plugins('afterPostCreate');

		// Alert for the user
		Alert::set($Language->g('Post added successfully'));
		Redirect::page($adminfolder, 'manage-posts');
	}
	else {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the post.');
		Log::set(__METHOD__.LOG_SEP.'Cleaning database...');
		$dbPosts->delete($key);
	}

	return false;
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	addPost($_POST);
}

// ============================================================================
// Main after POST
// ============================================================================
