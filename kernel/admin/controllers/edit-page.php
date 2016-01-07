<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

function editPage($args)
{
	global $dbPages;
	global $Language;

	if(!isset($args['parent'])) {
		$args['parent'] = NO_PARENT_CHAR;
	}

	// Add the page, if the $key is FALSE the creation of the post failure.
	$key = $dbPages->edit($args);

	if($key)
	{
		$dbPages->regenerateCli();

		// Call the plugins after page created.
		Theme::plugins('afterPageModify');

		// Alert the user
		Alert::set($Language->g('The changes have been saved'));
		Redirect::page('admin', 'edit-page/'.$args['slug']);
	}
	else
	{
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to edit the page.');
	}
}

function deletePage($key)
{
	global $dbPages;
	global $Language;

	if( $dbPages->delete($key) )
	{
		// Call the plugins after post created.
		Theme::plugins('afterPageDelete');

		Alert::set($Language->g('The page has been deleted successfully'));
		Redirect::page('admin', 'manage-pages');
	}
	else
	{
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the page.');
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
	if( isset($_POST['delete-page']) ) {
		deletePage($_POST['key']);
	}
	else {
		editPage($_POST);
	}
}

// ============================================================================
// Main after POST
// ============================================================================

if(!$dbPages->pageExists($layout['parameters']))
{
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the page: '.$layout['parameters']);
	Redirect::page('admin', 'manage-pages');
}

$_Page = $pages[$layout['parameters']];
