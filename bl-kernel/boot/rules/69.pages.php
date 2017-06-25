<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

// Array with all published pages
$pages = array();

// Array with all pages (published, fixed, sticky, draft, scheduled)
$allPages = array();

// Object Page for the page filtered by the user
$page = false;

// Array with all page parents published
//$pageParents = array();

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
	$page = buildPage( $Url->slug() );

	// The page doesn't exist
	if($page===false) {
		$Url->setNotFound(true);
	}
	// The page is not published, still scheduled or draft
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

// Set page 404 not found
if( $Url->notFound() ) {
	$Url->setWhereAmI('page');
	$page = buildPage('error');
	$pages[0] = $page;
}
