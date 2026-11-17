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

// Search, matches the title, the URL and the tags of the content.
// Uses the pages database already in memory, the content files are not read.
$search = $url->parameter('search');
$search = is_string($search) ? Text::cut(trim($search), 0, 100) : '';
$searchResults = array();
if ($search !== '') {
	$isAuthor = checkRole(array('author'), false);
	foreach ($pages->db as $pageKey => $fields) {
		if ($fields['type'] === 'autosave') {
			continue;
		}
		if ($isAuthor && $fields['username'] !== $login->username()) {
			continue;
		}
		$haystack = Sanitize::htmlDecode($fields['title']) . ' ' . $pageKey;
		if (!empty($fields['tags'])) {
			$haystack .= ' ' . Sanitize::htmlDecode(implode(' ', $fields['tags']));
		}
		if (Text::stringContains($haystack, $search, false)) {
			$searchResults[] = $pageKey;
		}
	}
}

// Check if out of range the pageNumber
if (empty($published) && $url->pageNumber()>1) {
	Redirect::page('content');
}

// Title of the page
$layout['title'] .= ' - '.$L->g('Manage content');