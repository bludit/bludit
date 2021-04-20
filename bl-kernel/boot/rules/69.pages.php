<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Variables
// ============================================================================

// Array with pages, each page is a Page Object
// Filtered by pagenumber, number of items per page and sorted by date/position
/*
	array(
		0 => Page Object,
		1 => Page Object,
		...
		N => Page Object
	)
*/
$content = array();

// Page filtered by the user, will be a Page Object
$page = false;

// Array with static content, each item is a Page Object
// Order by position
/*
	array(
		0 => Page Object,
		1 => Page Object,
		...
		N => Page Object
	)
*/
$staticContent = buildStaticPages();

// ============================================================================
// Main
// ============================================================================

// Execute the scheduler
if ($pages->scheduler()) {
	// Execute plugins with the hook afterPageCreate
	execPluginsByHook('afterPageCreate');

	reindexTags();
        reindexCategories();

	// Add to syslog
	$syslog->add(array(
		'dictionaryKey'=>'content-published-from-scheduler',
		'notes'=>''
	));
}

// Set home page if the user defined one
if ($site->homepage() && $url->whereAmI()==='home') {
	$pageKey = $site->homepage();
	if ($pages->exists($pageKey)) {
		$url->setSlug($pageKey);
		$url->setWhereAmI('page');
	}
}

// Build specific page
if ($url->whereAmI()==='page') {
	$page = buildThePage();
}
// Build content by tag
elseif ($url->whereAmI()==='tag') {
	$content = buildPagesByTag();
}
// Build content by category
elseif ($url->whereAmI()==='category') {
	$content = buildPagesByCategory();
}
// Build content for the homepage
elseif ( ($url->whereAmI()==='home') || ($url->whereAmI()==='blog') ) {
    $content = buildPagesForHome();
}

// If set notFound, create the page 404
if ($url->notFound()) {
	$page = buildErrorPage();
}
