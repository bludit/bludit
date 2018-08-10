<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_POST
// ----------------------------------------------------------------------------
// (integer) $_POST['pageNumber'] > 0
$pageNumber = !empty($_POST['pageNumber']) ? (int)$_POST['pageNumber'] : 1;
$pageNumber = $pageNumber - 1;

// (string) $_POST['path']
$path = isset($_POST['path']) ? $_POST['path'] : false;
// ----------------------------------------------------------------------------
if ($path=='thumbnails') {
	$path = PATH_UPLOADS_THUMBNAILS;
} else {
	exit (json_encode(array(
		'status'=>1,
		'files'=>'Invalid path.'
	)));
}

// Get all files from the directory $path, also split the array by numberOfItems
$listOfFilesByPage = Filesystem::listFiles($path, '*', '*', $GLOBALS['BLUDIT_MEDIA_MANAGER_SORT_BY_DATE'], $GLOBALS['BLUDIT_MEDIA_MANAGER_AMOUNT_OF_FILES']);

// Check if the page number exists in the chunks
if (isset($listOfFilesByPage[$pageNumber])) {

	// Get only the filename from the chunk
	$tmp = array();
	foreach ($listOfFilesByPage[$pageNumber] as $file) {
		array_push($tmp, basename($file));
	}

	// Returns the number of chunks for the paginator
	// Returns the files inside the chunk
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