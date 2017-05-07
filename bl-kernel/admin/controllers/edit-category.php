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

	if( Text::isEmpty($oldCategoryKey) || Text::isEmpty($newCategory) ) {
		Alert::set($Language->g('Empty field'));
		Redirect::page('admin', 'categories');
	}
	
	if( $dbCategories->edit($oldCategoryKey, $newCategory) == false ) {
		Alert::set($Language->g('Already exist a category'));
	}
	else {
		$dbPages->changeCategory($oldCategoryKey, $newCategory);
		$dbPosts->changeCategory($oldCategoryKey, $newCategory);
		Alert::set($Language->g('The changes have been saved'));
	}

	Redirect::page('admin', 'categories');
}

function delete($categoryKey)
{
	global $Language;
	global $dbCategories;

	$dbCategories->remove($categoryKey);

	Alert::set($Language->g('The changes have been saved'));

	Redirect::page('admin', 'categories');
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
		edit($_POST['categoryKey'], $_POST['category']);
	}
}

// ============================================================================
// Main after POST
// ============================================================================

$categoryKey = $layout['parameters'];

if(!$dbCategories->exists($categoryKey)) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the category: '.$categoryKey);
	Redirect::page('admin', 'categories');
}

$category = $dbCategories->getName($layout['parameters']);

$layout['title'] .= ' - '.$Language->g('Edit category').' - '.$category;
