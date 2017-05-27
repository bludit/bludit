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
	global $Syslog;

	if(!isset($args['parent'])) {
		$args['parent'] = NO_PARENT_CHAR;
	}

	// Edit the page
	$key = $dbPages->edit($args);

	if($key) {
		// Re-index categories
		reindexCategories();

		// Re-index tags
		reindextags();

		// Call the plugins after page modified
		Theme::plugins('afterPageModify');

		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'page-edited',
			'notes'=>$args['title']
		));

		// Create an alert
		Alert::set( $Language->g('The changes have been saved') );

		// Redirect
		Redirect::page('edit-page/'.$key);
	}
	else {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to edit the page.');
	}

	return false;
}

function deletePage($key)
{
	global $dbPages;
	global $Language;
	global $Syslog;

	if( $dbPages->delete($key) ) {
		// Re-index categories
		reindexCategories();

		// Re-index tags
		reindextags();

		// Call the plugins after page deleted
		Theme::plugins('afterPageDelete');

		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'page-deleted',
			'notes'=>$key
		));

		// Create an alert
		Alert::set( $Language->g('The changes have been saved') );

		// Redirect
		Redirect::page('pages');
	}
	else {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the page.');
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

if( !$dbPages->exists($layout['parameters']) ) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the page: '.$layout['parameters']);
	Redirect::page('pages');
}

$page = $pagesByKey[$layout['parameters']];
