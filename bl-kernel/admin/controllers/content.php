<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin', 'editor', 'author'));

// ============================================================================
// Functions
// ============================================================================

// Returns the content belongs to the current user if the user has the role Editor
function filterContentOwner($list) {
	global $login;
	global $pages;
	$tmp = array();
	foreach ($list as $pageKey) {
		if ($pages->db[$pageKey]['username']==$login->username()) {
			array_push($tmp, $pageKey);
		}
	}
	return $tmp;
}

// ============================================================================
// Main before POST
// ============================================================================

// ============================================================================
// POST Method
// ============================================================================

// ============================================================================
// Main after POST
// ============================================================================

$published = $pages->getList($url->pageNumber(), ITEMS_PER_PAGE_ADMIN);
$drafts = $pages->getDraftDB(true);
$scheduled = $pages->getScheduledDB(true);
$static = $pages->getStaticDB(true);
$sticky = $pages->getStickyDB(true);
$autosave = $pages->getAutosaveDB(true);

// If the user is an Author filter the content he/she can edit
if (checkRole(array('author'), false)) {
	$published 	= filterContentOwner($published);
	$drafts 	= filterContentOwner($drafts);
	$scheduled 	= filterContentOwner($scheduled);
	$static 	= filterContentOwner($static);
	$sticky 	= filterContentOwner($sticky);
}

// Check if out of range the pageNumber
if (empty($published) && $url->pageNumber()>1) {
	Redirect::page('content');
}

// Title of the page
$layout['title'] .= ' - '.$L->g('Manage content');