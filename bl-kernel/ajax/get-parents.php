<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Returns a list of parent pages and the title contains the query string
| The returned list have published, sticky and statics pages
|
| @_POST['query']	string 	The string to search in the title of the pages
|
| @return	array
*/

// $_GET
// ----------------------------------------------------------------------------
// (string) $_GET['query']
$query = isset($_GET['query']) ? Text::lowercase($_GET['query']) : false;
// ----------------------------------------------------------------------------
if ($query===false) {
	ajaxResponse(1, 'Invalid query.');
}

$tmp = array();
$pagesKey = $pages->getDB();
foreach ($pagesKey as $pageKey) {
	try {
		$page = new Page($pageKey);
		// Check if the page is available to be parent
		if ($page->isParent()) {
			// Check page status
			if ($page->published() || $page->sticky() || $page->isStatic()) {
				// Check if the query contains in the title
				$lowerTitle = Text::lowercase($page->title());
				if (Text::stringContains($lowerTitle, $query)) {
					$tmp[$page->title()] = $page->key();
				}
			}
		}
	} catch (Exception $e) {
		// continue
	}
}

exit (json_encode($tmp));

?>