<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Functions
// ============================================================================

function editPage($args)
{
	global $dbPages;

	// Page status, published or draft.
	if( isset($args['publish']) ) {
		$args['status'] = "published";
	}
	else {
		$args['status'] = "draft";
	}

	if(!isset($args['parent'])) {
		$args['parent'] = NO_PARENT_CHAR;
	}

	// Edit the page.
	if( $dbPages->edit($args) )
	{
		$dbPages->regenerate();

		Alert::set('The page has been saved successfully');
		Redirect::page('admin', 'manage-pages');
	}
	else
	{
		Alert::set('Error occurred when trying to edit the page');
	}
}

function deletePage($key)
{
	global $dbPages;

	if( $dbPages->delete($key) )
	{
		Alert::set('The page has been deleted successfully');
		Redirect::page('admin', 'manage-pages');
	}
	else
	{
		Alert::set('Error occurred when trying to delete the page');
	}
}

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	if( isset($_POST['delete']) ) {
		deletePage($_POST['key']);
	}
	else {
		editPage($_POST);
	}
}

// ============================================================================
// Main
// ============================================================================

if(!$dbPages->pageExists($layout['parameters'])) {
	Redirect::page('admin', 'manage-pages');
}

$_Page = $pages[$layout['parameters']];
