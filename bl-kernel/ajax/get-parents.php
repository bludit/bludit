<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Returns a list of pages are parent and match with the string in them titles
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
$parents = buildParentPages();
foreach ($parents as $parent) {
	$lowerTitle = Text::lowercase($parent->title());
	if (Text::stringContains($lowerTitle, $query)) {
		$tmp[$parent->title()] = $parent->key();
	}
}

exit (json_encode($tmp));

?>