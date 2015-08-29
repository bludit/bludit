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
	global $dbTags;
	global $Language;

	// Page status, published or draft.
	if( isset($args['publish']) ) {
		$args['status'] = "published";
	}
	else {
		$args['status'] = "draft";
	}

	// Add the page.
	if( $dbPosts->add($args) )
	{
		// Regenerate the database tags
		$dbPosts->removeUnpublished();
		$dbPosts->sortByDate();
		$dbTags->reindexPosts( $dbPosts->db );

		Alert::set($Language->g('Post added successfully'));
		Redirect::page('admin', 'manage-posts');
	}
	else
	{
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the post.');
	}
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