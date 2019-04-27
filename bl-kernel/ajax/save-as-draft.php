<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_POST
// ----------------------------------------------------------------------------
// (string) $_POST['title']
$title = isset($_POST['title']) ? $_POST['title'] : false;
// (string) $_POST['content']
$content = isset($_POST['content']) ? $_POST['content'] : false;
// (string) $_POST['uuid']
$uuid = isset($_POST['uuid']) ? $_POST['uuid'] : false;
// ----------------------------------------------------------------------------

// Check UUID
if (empty($uuid)) {
	ajaxResponse(1, 'Autosave fail. UUID not defined.');
}

$autosaveUUID = 'autosave-'.$uuid;
$page = array(
	'uuid'=>$autosaveUUID,
	'key'=>$autosaveUUID,
	'slug'=>$autosaveUUID,
	'title'=>$title.' [ Autosave ] ',
	'content'=>$content,
	'type'=>'draft'
);

// Get the page key by the UUID
$pageKey = $pages->getByUUID($autosaveUUID);

// if pageKey is empty means the autosave page doesn't exist
if (empty($pageKey)) {
	createPage($page);
} else {
	editPage($page);
}

ajaxResponse(0, 'Autosave successfully.', array(
	'uuid'=>$autosaveUUID
));

?>