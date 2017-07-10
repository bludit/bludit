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
	global $dbPages;
	global $dbCategories;

	if( Text::isEmpty($oldCategoryKey) || Text::isEmpty($newCategory) ) {
		Alert::set($Language->g('Empty fields'));
		Redirect::page('categories');
	}

	if( $dbCategories->edit($oldCategoryKey, $newCategory) == false ) {
		Alert::set($Language->g('Already exist a category'));
	}
	else {
		$dbPages->changeCategory($oldCategoryKey, $newCategory);
		Alert::set($Language->g('The changes have been saved'));
	}

	// Add to syslog
	$Syslog->add(array(
		'dictionaryKey'=>'category-edited',
		'notes'=>$newCategory
	));

	Redirect::page('categories');
}

function delete($categoryKey)
{
	global $Language;
	global $dbCategories;

	// Remove the category by key
	$dbCategories->remove($categoryKey);

	// Add to syslog
	$Syslog->add(array(
		'dictionaryKey'=>'category-deleted',
		'notes'=>$categoryKey
	));

	// Create an alert
	Alert::set($Language->g('The changes have been saved'));

	// Redirect
	Redirect::page('categories');
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

if( !$dbCategories->exists($categoryKey) ) {
	Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to get the category: '.$categoryKey);
	Redirect::page('categories');
}

$category = $dbCategories->getName($layout['parameters']);

$layout['title'] .= ' - '.$Language->g('Edit Category').' - '.$category;
