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

$tmp = array();
$published = $pages->getPublishedDB();
$statics = $pages->getStaticDB();
$pagesKey = array_merge($published, $statics);
foreach ($pagesKey as $pageKey) {
	try {
		$page = new Page($pageKey);
		if ($page->isParent()) {
			$lowerTitle = Text::lowercase($page->title());
			if (Text::stringContains($lowerTitle, $query)) {
				$tmp[$page->title()] = $page->key();
			}
		}
	} catch (Exception $e) {
		// continue
	}
}

exit (json_encode($tmp));

?>