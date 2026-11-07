<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Create/edit the autosave page of the editor
| If the UUID already exists the autosave page is updated
|
| @_POST['title']	string	Page title
| @_POST['content']	string	Page content
| @_POST['uuid']	string	Autosave uuid, "autosave-" followed by the page uuid
|
| @return	array
*/

// $_POST
// ----------------------------------------------------------------------------
$title = isset($_POST['title']) ? $_POST['title'] : false;
$content = isset($_POST['content']) ? $_POST['content'] : false;
$uuid = isset($_POST['uuid']) ? $_POST['uuid'] : false;
// ----------------------------------------------------------------------------

// Check UUID
if (empty($uuid)) {
	ajaxResponse(1, 'Save as draft fail. UUID not defined.');
}

// This endpoint only stores the autosave of the editor, it can't create or edit other pages
if (!$pages->isValidUUID($uuid) || (strpos($uuid, 'autosave-') !== 0)) {
	ajaxResponse(1, 'Save as draft fail. Invalid UUID.');
}

// Get the page key by the UUID
$pageKey = $pages->getByUUID($uuid);

// Authors can only autosave their own pages
// The owner of the page is checked, or the owner of the autosave when the page is not created yet
if (checkRole(array('author'), false)) {
	$ownerKey = $pages->getByUUID(substr($uuid, strlen('autosave-')));
	if (empty($ownerKey)) {
		$ownerKey = $pageKey;
	}
	if (!empty($ownerKey) && ($pages->db[$ownerKey]['username'] !== $login->username())) {
		ajaxResponse(1, $L->g('You do not have sufficient permissions'));
	}
}

$page = array(
	'uuid'=>$uuid,
	'title'=>$title,
	'content'=>$content,
	'type'=>'autosave'
);

// if pageKey is empty means the page doesn't exist
if (empty($pageKey)) {
	$page['key'] = $uuid;
	$page['slug'] = $uuid;
	$result = createPage($page);
} else {
	// Edit the page found by the UUID, not a page with the same key
	$page['key'] = $pageKey;
	$page['slug'] = $pageKey;
	$result = editPage($page);
}

if ($result === false) {
	ajaxResponse(1, 'Save as draft fail.');
}

ajaxResponse(0, 'Save as draft successfully.', array(
	'uuid'=>$uuid
));

?>