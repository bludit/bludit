<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

$text 	= isset($_POST['text']) ? $_POST['text'] : '';
$parent = isset($_POST['parentKey']) ? $_POST['parentKey'] : PARENT;
$oldKey = isset($_POST['currentKey']) ? $_POST['currentKey'] : '';

$slug = $dbPages->generateKey($text, $parent, $returnSlug=true, $oldKey);

exit (json_encode(array(
	'status'=>0,
	'slug'=>$slug
)));

?>