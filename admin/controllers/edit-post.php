<?php defined('BLUDIT') or die('Bludit CMS.');

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
		Alert::set($Language->g('the-changes-have-been-saved'));
		Redirect::page('admin', 'edit-post/'.$args['key']);
	}
	else
	{
		Alert::set($Language->g('an-error-occurred-while-trying-to-edit-the-post'));
	}
}

function deletePost($key)
{
	global $dbPosts;

	if( $dbPosts->delete($key) )
	{
		Alert::set('The post has been deleted successfull');
		Redirect::page('admin', 'manage-posts');
	}
	else
	{
		Alert::set('an-error-occurred-while-trying-to-delete-the-post');
	}
}

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
// Main
// ============================================================================

if(!$dbPosts->postExists($layout['parameters'])) {
	Redirect::page('admin', 'manage-posts');
}

$_Post = buildPost($layout['parameters']);

$layout['title'] .= ' - '.$Language->g('Edit post').' - '.$_Post->title();