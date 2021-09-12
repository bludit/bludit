<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Authorization
// ============================================================================

checkRole(array('admin', 'editor', 'author'));

// ============================================================================
// Functions
// ============================================================================

// Returns the content belongs to the current logged user
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
// Main
// ============================================================================

$published = $pages->getList($url->pageNumber(), ITEMS_PER_PAGE_ADMIN);
$drafts = $pages->getDraftDB(true);
$scheduled = $pages->getScheduledDB(true);
$static = $pages->getStaticDB(true);
$sticky = $pages->getStickyDB(true);
$unlisted = $pages->getUnlistedDB(true);

// If the user has the role "Author" filter the content so he/she can edit
if (checkRole(array('author'), false)) {
	$published 	= filterContentOwner($published);
	$drafts 	= filterContentOwner($drafts);
	$scheduled 	= filterContentOwner($scheduled);
	$static 	= filterContentOwner($static);
	$sticky 	= filterContentOwner($sticky);
    $unlisted 	= filterContentOwner($unlisted);
}

// Check if the page number is out of range
if (empty($published) && $url->pageNumber()>1) {
	Redirect::page('content');
}

// View HTML <title>
$layout['title'] = $L->g('Manage content') . ' - ' . $layout['title'];
