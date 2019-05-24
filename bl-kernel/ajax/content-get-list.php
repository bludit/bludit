<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Search for pages that have in the title the string $query and returns the array of pages
|
| @_GET['published']	boolean	True to search in published database
| @_GET['static']	boolean True to search in static database
| @_GET['sticky']	boolean True to search in sticky database
| @_GET['scheduled']	boolean True to search in scheduled database
| @_GET['draft']	boolean True to search in draft database
| @_GET['query']	string	Text to search in the title
|
| @return		array
*/

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
			$tmp[$page->key()] = $page->json(true);
		}
	} catch (Exception $e) {
		// continue
	}
}

exit (json_encode($tmp));

?>