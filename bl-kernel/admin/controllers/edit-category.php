<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if ($Login->role()!=='admin') {
	Alert::set($Language->g('You do not have sufficient permissions'));
	Redirect::page('dashboard');
}

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
		deleteCategory($_POST['categoryKey']);
	}
	elseif (isset($_POST['edit'])) {
		editCategory($_POST['categoryKey'], $_POST['category']);
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

$category = $dbCategories->getName($layout['parameters']);

// Title of the page
$layout['title'] .= ' - '.$Language->g('Edit Category').' - '.$category;