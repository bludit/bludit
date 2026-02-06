<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Generate an slug text for the URL
|
| @_POST['text']	string 	The text from where is generated the slug
| @_POST['parentKey']	string	The parent key if the page has one
| @_POST['currentKey']	string	The current page key
|
| @return	array
*/

// $_POST
// ----------------------------------------------------------------------------
$text 	= isset($_POST['text']) ? $_POST['text'] : '';
$parent = isset($_POST['parentKey']) ? $_POST['parentKey'] : '';
$oldKey = isset($_POST['currentKey']) ? $_POST['currentKey'] : '';
// ----------------------------------------------------------------------------

$slug = $pages->generateKey($text, $parent, $returnSlug=true, $oldKey);

ajaxResponse(0, 'Slug generated.', array(
	'slug'=>$slug
));

?>