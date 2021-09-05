<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Returns a list of pages that the title contains the query string.
| The returned list have published, sticky and statics pages.
| It's possible to filter the pages are parents by the flag "checkIsParent".
|
| @_POST['query']			string 	The string to search in the title of the pages.
| @_POST['checkIsParent']	boolean	TRUE returns only parent pages, FALSE returns all pages.
|
| @return					json 	Ex. {"results":[{"disabled":false,"id":"follow-bludit","text":"Follow Bludit","type":"published"}]}
*/

// $_GET
// ----------------------------------------------------------------------------
// (string) $_GET['query']
$query = isset($_GET['query']) ? Text::lowercase($_GET['query']) : false;
// (boolean) $_GET['checkIsParent']
$checkIsParent = empty($_GET['checkIsParent']) ? false : true;
// ----------------------------------------------------------------------------
if ($query===false) {
    ajaxResponse(1, 'Invalid query.');
}

$result = array();
$pagesKey = $pages->getDB();
foreach ($pagesKey as $pageKey) {
    try {
        $page = new Page($pageKey);
        if ($page->isParent() || !$checkIsParent) {
            // Check page status
            if ($page->published() || $page->sticky() || $page->isStatic()) {
                // Check if the query contains in the title
                $lowerTitle = Text::lowercase($page->title());
                if (Text::stringContains($lowerTitle, $query)) {
                    $tmp = array('disabled'=>false);
                    $tmp['id'] = $page->key();
                    $tmp['text'] = $page->title();
                    $tmp['type'] = $page->type();
                    array_push($result, $tmp);
                }
            }
        }
    } catch (Exception $e) {
        // continue
    }
}

exit (json_encode(array('results'=>$result)));
