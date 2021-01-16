<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Authorization
// ============================================================================

checkRole(array('admin'));

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main
// ============================================================================

$categoryKey = $layout['parameters'];

if (!$categories->exists($categoryKey)) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the category: '.$categoryKey);
	Redirect::page('categories');
}

$categoryMap = $categories->getMap($categoryKey);

// HTML <title>
$layout['title'] = $L->g('Edit Category') . ' [ ' . $categoryMap['name'] . ' ] ' . ' - ' . $layout['title'];