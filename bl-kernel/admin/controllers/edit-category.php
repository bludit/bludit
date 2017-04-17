<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

if($Login->role()!=='admin') {
	Alert::set($Language->g('you-do-not-have-sufficient-permissions'));
	Redirect::page('admin', 'dashboard');
}

// ============================================================================
// Functions
// ============================================================================

function edit($oldCategoryKey, $newCategory)
{
	global $Language;
	global $dbPosts;
	global $dbPages;
	global $dbCategories;

	if( $dbCategories->edit($oldCategoryKey, $newCategory) == false ) {
		Alert::set($Language->g('Already exist a category'));
	}
	else {
		$dbPages->changeCategory($oldCategoryKey, $newCategory);
		$dbPosts->changeCategory($oldCategoryKey, $newCategory);
		Alert::set($Language->g('The changes have been saved'));
	}

	Redirect::page('admin', 'manage-categories');
}

function delete($categoryKey)
{
	global $Language;
	global $dbCategories;

	$dbCategories->remove($categoryKey);

	Alert::set($Language->g('The changes have been saved'));

	Redirect::page('admin', 'manage-categories');
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	if( isset($_POST['delete']) ) {
		delete($_POST['categoryKey']);
	}
	elseif( isset($_POST['edit']) ) {
		edit($_POST['categoryKey'], $_POST['categoryName']);
	}
}

// ============================================================================
// Main after POST
// ============================================================================

if(!$dbCategories->exists($categoryKey))
{
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the category: '.$layout['parameters']);
	Redirect::page('admin', 'manage-categories');
}

$categoryKey = $layout['parameters'];
$category = $dbCategories->getName($layout['parameters']);

$layout['title'] .= ' - '.$Language->g('Edit category').' - '.$category;
