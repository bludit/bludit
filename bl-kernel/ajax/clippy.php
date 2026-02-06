<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_GET
// ----------------------------------------------------------------------------
// (string) $_GET['query']
$query = isset($_GET['query']) ? Text::lowercase($_GET['query']) : false;
// ----------------------------------------------------------------------------
if ($query===false) {
	ajaxResponse(1, 'Invalid query.');
}

$result = array();

// MENU
if (Text::stringContains(Text::lowercase($L->g('New content')), $query)) {
	$tmp = array('disabled'=>true, 'icon'=>'plus-circle', 'type'=>'menu');
	$tmp['text'] = $L->g('New content');
	$tmp['url'] = HTML_PATH_ADMIN_ROOT.'new-content';
	array_push($result, $tmp);
}
if (Text::stringContains(Text::lowercase($L->g('New category')), $query)) {
	$tmp = array('disabled'=>true, 'icon'=>'tag', 'type'=>'menu');
	$tmp['text'] = $L->g('New category');
	$tmp['url'] = HTML_PATH_ADMIN_ROOT.'new-category';
	array_push($result, $tmp);
}
if (Text::stringContains(Text::lowercase($L->g('New user')), $query)) {
	$tmp = array('disabled'=>true, 'icon'=>'user', 'type'=>'menu');
	$tmp['text'] = $L->g('New user');
	$tmp['url'] = HTML_PATH_ADMIN_ROOT.'new-user';
	array_push($result, $tmp);
}
if (Text::stringContains(Text::lowercase($L->g('Categories')), $query)) {
	$tmp = array('disabled'=>true, 'icon'=>'tags', 'type'=>'menu');
	$tmp['text'] = $L->g('Categories');
	$tmp['url'] = HTML_PATH_ADMIN_ROOT.'categories';
	array_push($result, $tmp);
}
if (Text::stringContains(Text::lowercase($L->g('Users')), $query)) {
	$tmp = array('disabled'=>true, 'icon'=>'users', 'type'=>'menu');
	$tmp['text'] = $L->g('Users');
	$tmp['url'] = HTML_PATH_ADMIN_ROOT.'users';
	array_push($result, $tmp);
}


// PAGES
$pagesKey = $pages->getDB();
foreach ($pagesKey as $pageKey) {
	try {
		$page = new Page($pageKey);
		$lowerTitle = Text::lowercase($page->title());
		if (Text::stringContains($lowerTitle, $query)) {
			$tmp = array('disabled'=>true);
			$tmp['id'] = $page->key();
			$tmp['text'] = $page->title();
			$tmp['type'] = $page->type();
			array_push($result, $tmp);
		}
	} catch (Exception $e) {
		// continue
	}
}

exit (json_encode(array('results'=>$result)));

?>