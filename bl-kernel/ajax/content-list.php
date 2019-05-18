<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_GET
// ----------------------------------------------------------------------------
$published = empty($_GET['published']) ? false:true;
$static = empty($_GET['static']) ? false:true;
$sticky = empty($_GET['sticky']) ? false:true;
$scheduled = empty($_GET['scheduled']) ? false:true;
$draft = empty($_GET['draft']) ? false:true;
$query = isset($_GET['query']) ? Text::lowercase($_GET['query']) : false;
// ----------------------------------------------------------------------------

if ($query===false) {
	ajaxResponse(1, 'Invalid query.');
}

$pageNumber = 1;
$numberOfItems = -1;
$pagesKey = $pages->getList($pageNumber, $numberOfItems, $published, $static, $sticky, $draft, $scheduled);
$tmp = array();
foreach ($pagesKey as $pageKey) {
	try {
		$page = new Page($pageKey);
		$lowerTitle = Text::lowercase($page->title());
		if (Text::stringContains($lowerTitle, $query)) {
			$tmp[$page->title()] = $page->key();
		}
	} catch (Exception $e) {
		// continue
	}
}

exit (json_encode($tmp));

?>