<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Change a page's type. Allowed types: published, sticky, static, draft.
| Allowed transitions are restricted to the same set on both ends — anything
| else (autosave, scheduled) goes through the normal edit flow.
|
| @_POST['key']	string	Page key
| @_POST['type']	string	Target type
|
| @return	JSON { status, message, key, type }
*/

checkRole(array('admin', 'editor', 'author'));

$allowed = array('published', 'sticky', 'static', 'draft');

$key = isset($_POST['key']) ? Sanitize::html($_POST['key']) : false;
$newType = isset($_POST['type']) ? Sanitize::html($_POST['type']) : false;

if (empty($key) || !$pages->exists($key)) {
	ajaxResponse(1, $L->g('Page not found.'));
}

if (!in_array($newType, $allowed, true)) {
	ajaxResponse(1, $L->g('Target type is not allowed.'));
}

$current = $pages->db[$key]['type'];
if (!in_array($current, $allowed, true)) {
	ajaxResponse(1, $L->g('Current page type cannot be changed from this menu.'));
}

if ($current === $newType) {
	ajaxResponse(0, $L->g('Page type unchanged.'), array(
		'key' => $key,
		'type' => $newType
	));
}

// Authors can only change their own pages
if (checkRole(array('author'), false)) {
	if ($pages->db[$key]['username'] !== $login->username()) {
		ajaxResponse(1, $L->g('Permission denied.'));
	}
}

// Don't let a static parent be moved away from "static" while it has children;
// otherwise the descendants get orphaned in the static tree (getStaticDB filters by type).
if ($current === 'static' && $newType !== 'static') {
	try {
		$page = new Page($key);
		if (count($page->children()) > 0) {
			ajaxResponse(1, $L->g('Cannot change type while the page has children.'));
		}
	} catch (Exception $e) {
		ajaxResponse(1, $L->g('Page not found.'));
	}
}

if ($pages->setField($key, 'type', $newType) === false) {
	ajaxResponse(1, $L->g('Failed to update page type.'));
}

ajaxResponse(0, $L->g('Page type updated.'), array(
	'key' => $key,
	'type' => $newType
));
