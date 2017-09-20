<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if( isset($_POST['delete-page']) ) {
		if( deletePage($_POST['key']) ) {
			Alert::set( $Language->g('The changes have been saved') );
			Redirect::page('pages');
		}
	}
	else {
		$key = editPage($_POST);
		if( $key!==false ) {
			Alert::set( $Language->g('The changes have been saved') );
			Redirect::page('edit-page/'.$key);
		}
	}

	Redirect::page('pages');
}

// ============================================================================
// Main after POST
// ============================================================================
$pageKey = $layout['parameters'];
$page = buildPage($pageKey);
if ($page===false) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the page: '.$pageKey);
	Redirect::page('pages');
}

// Title of the page
$layout['title'] .= ' - '.$Language->g('Edit page').' - '.$page->title();