<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

function editPost($args)
{
	global $dbPosts;
	global $Language;

	// Post status, published or draft.
	if( isset($args['publish']) ) {
		$args['status'] = "published";
	}
	else {
		$args['status'] = "draft";
	}

	// Edit the post.
	if( $dbPosts->edit($args) )
	{
		// Reindex tags, this function is in 70.posts.php
		reIndexTagsPosts();

		Alert::set($Language->g('The changes have been saved'));
		Redirect::page('admin', 'edit-post/'.$args['key']);
	}
	else
	{
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to edit the post.');
	}

	return false;
}

function deletePost($key)
{
	global $dbPosts;
	global $Language;

	if( $dbPosts->delete($key) )
	{
		// Reindex tags, this function is in 70.posts.php
		reIndexTagsPosts();

		Alert::set($Language->g('The post has been deleted successfully'));
		Redirect::page('admin', 'manage-posts');
	}
	else
	{
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the post.');
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
	if( isset($_POST['delete']) ) {
		deletePost($_POST['key']);
	}
	else {
		editPost($_POST);
	}
}

// ============================================================================
// Main after POST
// ============================================================================

if(!$dbPosts->postExists($layout['parameters']))
{
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the post: '.$layout['parameters']);
	Redirect::page('admin', 'manage-posts');
}

$_Post = buildPost($layout['parameters']);

$layout['title'] .= ' - '.$Language->g('Edit post').' - '.$_Post->title();