<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_GET
// (integer) $_GET['pageNumber']
$pageNumber = isset($_GET['pageNumber']) ? (int)$_GET['pageNumber'] : '1';
$pageNumber = $pageNumber - 1;

$numberOfItems = 10; // Number of items per page
$sortByDate=true;

// Get all files from the directory PATH_UPLOADS, also split the array by numberOfItems
$listOfFilesByPage = Filesystem::listFiles(PATH_UPLOADS, '*', '*', $sortByDate, $numberOfItems);

// Check if the page number exists in the chunks
if (isset($listOfFilesByPage[$pageNumber])) {

	// Get the filename from the chunk
	$tmp = array();
	foreach ($listOfFilesByPage[$pageNumber] as $file) {
		array_push($tmp, basename($file));
	}

	exit (json_encode(array(
		'status'=>0,
		'numberOfPages'=>count($listOfFilesByPage),
		'files'=>$tmp
	)));
}

exit (json_encode(array(
	'status'=>1,
	'files'=>'Out of index.'
)));

?>