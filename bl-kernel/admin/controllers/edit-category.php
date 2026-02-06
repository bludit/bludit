<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin'));

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
	if ($_POST['action']=='delete') {
		deleteCategory($_POST);
	} elseif ($_POST['action']=='edit') {
		editCategory($_POST);
	}

	Redirect::page('categories');
}

// ============================================================================
// Main after POST
// ============================================================================
$categoryKey = $layout['parameters'];

if (!$categories->exists($categoryKey)) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the category: '.$categoryKey);
	Redirect::page('categories');
}

$categoryMap = $categories->getMap($categoryKey);

// Title of the page
$layout['title'] .= ' - '.$L->g('Edit Category').' [ '.$categoryMap['name'] . ' ] ';