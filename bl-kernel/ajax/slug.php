<?php defined('BLUDIT') or die('Bludit CMS.');

header('Content-Type: application/json');

// Request $_POST
//		type: page or post.
//		text: Slug to valid.
//		parent: Page parent, if you are checking a slug for a page.

// Response JSON
//		slug: valid slug text

$text 	= isset($_POST['text']) ? $_POST['text'] : '';
$parent = isset($_POST['parent']) ? $_POST['parent'] : NO_PARENT_CHAR;
$key 	= isset($_POST['key']) ? $_POST['key'] : '';

if( $_POST['type']==='page' ) {
	$slug = $dbPages->generateKey($text, $parent, true, $key);
}
elseif( $_POST['type']==='post' ) {
	$slug = $dbPosts->generateKey($text, $key);
}

echo json_encode( array('status'=>1, 'slug'=>$slug) );

?>