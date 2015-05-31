<?php defined('BLUDIT') or die('Bludit CMS.');

function addPost($args)
{
	global $dbPosts;

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
		Alert::set('Post added successfuly.');
		Redirect::page('admin', 'manage-posts');
	}
	else
	{
		Alert::set('Error occurred when trying to create the post.');
	}
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	addPost($_POST);
}
