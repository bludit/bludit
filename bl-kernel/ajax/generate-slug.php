<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

$text 	= isset($_POST['text']) ? $_POST['text'] : '';
$parent = isset($_POST['parentKey']) ? $_POST['parentKey'] : '';
$oldKey = isset($_POST['currentKey']) ? $_POST['currentKey'] : '';

$slug = $pages->generateKey($text, $parent, $returnSlug=true, $oldKey);

ajaxResponse(0, 'Slug generated.', array(
	'slug'=>$slug
));

?>