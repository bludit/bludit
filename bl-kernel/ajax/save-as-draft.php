<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Create/edit a page and save as draft
| If the UUID already exists the page is updated
|
| @_POST['title']	string	Page title
| @_POST['content']	string	Page content
| @_POST['uuid']	string	Page uuid
| @_POST['uuid']	string	Page type, by default is draft
|
| @return	array
*/

// $_POST
// ----------------------------------------------------------------------------
$title = isset($_POST['title']) ? $_POST['title'] : false;
$content = isset($_POST['content']) ? $_POST['content'] : false;
$uuid = isset($_POST['uuid']) ? $_POST['uuid'] : false;
$type = isset($_POST['type']) ? $_POST['type'] : 'draft';
// ----------------------------------------------------------------------------

// Check UUID
if (empty($uuid)) {
	ajaxResponse(1, 'Save as draft fail. UUID not defined.');
}

$page = array(
	'uuid'=>$uuid,
	'key'=>$uuid,
	'slug'=>$uuid,
	'title'=>$title,
	'content'=>$content,
	'type'=>$type
);

// Get the page key by the UUID
$pageKey = $pages->getByUUID($uuid);

// if pageKey is empty means the page doesn't exist
if (empty($pageKey)) {
	createPage($page);
} else {
	editPage($page);
}

ajaxResponse(0, 'Save as draft successfully.', array(
	'uuid'=>$uuid
));

?>