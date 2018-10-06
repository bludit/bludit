<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin', 'editor'));

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
	// If the checkbox is not selected the form doesn't send the field
	$_POST['noindex'] = isset($_POST['noindex'])?true:false;
	$_POST['nofollow'] = isset($_POST['nofollow'])?true:false;
	$_POST['noarchive'] = isset($_POST['noarchive'])?true:false;

	createPage($_POST);
	Redirect::page('content');
}

// ============================================================================
// Main after POST
// ============================================================================

// UUID of the page is need it for autosave and media manager
$uuid = $pages->generateUUID();

// Title of the page
$layout['title'] = $L->g('New content').' - '.$layout['title'];