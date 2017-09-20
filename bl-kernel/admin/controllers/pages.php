<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

// ============================================================================
// Main after POST
// ============================================================================

// List of published pages
$onlyPublished = true;
$amountOfItems = ITEMS_PER_PAGE_ADMIN;
$pageNumber = $Url->pageNumber();
$published = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);

// Check if out of range the pageNumber
if (empty($published)) {
	Redirect::page('pages');
}

// List of drafts pages
$drafts = $dbPages->getDraftDB();

$scheduled = $dbPages->getScheduledDB();

$static = $dbPages->getStaticDB();

// Title of the page
$layout['title'] .= ' - '.$Language->g('Manage Content');