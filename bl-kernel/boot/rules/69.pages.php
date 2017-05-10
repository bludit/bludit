<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

// Array with all published pages
$pages = array();

// Array with all pages (published, draft, scheduled)
$allPages = array();

// Object Page for the page filtered bye the user
$page = false;

// ============================================================================
// Main
// ============================================================================

// Execute the scheduler
if( $dbPages->scheduler() ) {
	// Reindex tags
	reindexTags();

        // Reindex categories
        reindexCategories();
}

// Build specific page
if( $Url->whereAmI()==='page' ) {

        // Build the page
	$page = buildPage( $Url->slug() );

	// The page doesn't exist
	if($page===false) {
		$Url->setNotFound(true);
	}
	// The page is not published
	elseif( !$page->published() ) {
		$Url->setNotFound(true);
	}
}
elseif( $Url->whereAmI()==='tag' ) {
        $tagKey = $Url->slug();
	$pages = buildPagesByTag($tagKey);
}
elseif( $Url->whereAmI()==='category' ) {
	$categoryKey = $Url->slug();
        $pages = buildPagesByCategory($categoryKey);
}
elseif( $Url->whereAmI()==='home' ) {
        $pages = buildPagesForHome();
}
elseif( $Url->whereAmI()==='admin' ) {
        $pages = buildPagesForAdmin();
}

if( $Url->notFound() ) {
	$Url->setWhereAmI('page');
	$page = new Page('error');
}
