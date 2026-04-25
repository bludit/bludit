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
	ajaxResponse(1, 'Page not found.');
}

if (!in_array($newType, $allowed, true)) {
	ajaxResponse(1, 'Target type is not allowed.');
}

$current = $pages->db[$key]['type'];
if (!in_array($current, $allowed, true)) {
	ajaxResponse(1, 'Current page type cannot be changed from this menu.');
}

if ($current === $newType) {
	ajaxResponse(0, 'Page type unchanged.', array(
		'key' => $key,
		'type' => $newType
	));
}

// Authors can only change their own pages
if (checkRole(array('author'), false)) {
	if ($pages->db[$key]['username'] !== $login->username()) {
		ajaxResponse(1, 'Permission denied.');
	}
}

if ($pages->setField($key, 'type', $newType) === false) {
	ajaxResponse(1, 'Failed to update page type.');
}

ajaxResponse(0, 'Page type updated.', array(
	'key' => $key,
	'type' => $newType
));
