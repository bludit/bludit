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
	if (isset($_POST['delete'])) {
		deleteCategory($_POST);
	} elseif (isset($_POST['edit'])) {
		editCategory($_POST);
	}

	Redirect::page('categories');
}

// ============================================================================
// Main after POST
// ============================================================================
$categoryKey = $layout['parameters'];

if (!$dbCategories->exists($categoryKey)) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the category: '.$categoryKey);
	Redirect::page('categories');
}

$categoryMap = $dbCategories->getMap($categoryKey);

// Title of the page
$layout['title'] .= ' - '.$Language->g('Edit Category').' [ '.$categoryMap['name'] . ' ] ';