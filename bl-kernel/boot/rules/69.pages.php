<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

// Array with pages, each page is a Object Page
$pages = array();

// Page filtered by the user, is a Object Page
$page = $Page = false;

// Array with pages order by parent
/*
array(
	PARENT => array(), // all parent pages
	parentKey1 => array(), // all children of parentKey1
	parentKey2 => array(), // all children of parentKey2
	...
	parentKeyN => array(), // all children of parentKeyN
)
*/
$pagesByParent = array(PARENT=>array());

// Array with all published pages, the array is a key=>Page-object
$pagesByKey = array();

// ============================================================================
// Main
// ============================================================================

// Execute the scheduler
if( $dbPages->scheduler() ) {
	// Reindex tags
	reindexTags();

        // Reindex categories
        reindexCategories();

	// Add to syslog
	$Syslog->add(array(
		'dictionaryKey'=>'page-published-from-scheduler',
		'notes'=>''
	));
}

// Build specific page
if( $Url->whereAmI()==='page' ) {
        // Build the page
	$page = $Page = buildPage( $Url->slug() );

	// The page doesn't exist
	if($page===false) {
		$Url->setNotFound(true);
	}
	// The page is not published, scheduled or draft
	elseif( $page->scheduled() || $page->draft() ) {
		$Url->setNotFound(true);
	}
	else {
		$pages[0] = $page;
	}
}
elseif( $Url->whereAmI()==='tag' ) {
	buildPagesByTag();
}
elseif( $Url->whereAmI()==='category' ) {
        buildPagesByCategory();
}
elseif( $Url->whereAmI()==='home' ) {
        buildPagesForHome();
}
elseif( $Url->whereAmI()==='admin' ) {
        buildPagesForAdmin();
}

if(ORDER_BY==='position') {
	$allPages = false; // All pages are published, draft, scheduled
	buildPagesByParent(false);
}

// Set page 404 not found
if( $Url->notFound() ) {
	$Url->setWhereAmI('page');
	$page = buildPage('error');
	$pages[0] = $page;
}
