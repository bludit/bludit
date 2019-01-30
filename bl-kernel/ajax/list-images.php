<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_POST
// ----------------------------------------------------------------------------
// (integer) $_POST['pageNumber'] > 0
$pageNumber = empty($_POST['pageNumber']) ? 1 : (int)$_POST['pageNumber'];
$pageNumber = $pageNumber - 1;

// (string) $_POST['path']
$path = empty($_POST['path']) ? false : $_POST['path'];

// (string) $_POST['uuid']
$uuid = empty($_POST['uuid']) ? false : $_POST['uuid'];
// ----------------------------------------------------------------------------

// Set the path to get the file list
if ($path=='thumbnails') {
	if ($uuid && IMAGE_RESTRICT) {
		$path = PATH_UPLOADS_PAGES.$uuid.DS.'thumbnails'.DS;
	} else {
		$path = PATH_UPLOADS_THUMBNAILS;
	}
} else {
	ajaxResponse(1, 'Invalid path.');
}

// Get all files from the directory $path, also split the array by numberOfItems
// The function listFiles split in chunks
$listOfFilesByPage = Filesystem::listFiles($path, '*', '*', $GLOBALS['MEDIA_MANAGER_SORT_BY_DATE'], $GLOBALS['MEDIA_MANAGER_NUMBER_OF_FILES']);

// Check if the page number exists in the chunks
if (isset($listOfFilesByPage[$pageNumber])) {

	// Get only the filename from the chunk
	$files = array();
	foreach ($listOfFilesByPage[$pageNumber] as $file) {
		$filename = basename($file);
		array_push($files, $filename);
	}

	// Returns the number of chunks for the paginator
	// Returns the files inside the chunk
	ajaxResponse(0, 'List of files and number of chunks.', array(
		'numberOfPages'=>count($listOfFilesByPage),
		'files'=>$files
	));
}

ajaxResponse(0, 'List of files and number of chunks.', array(
	'numberOfPages'=>0,
	'files'=>array()
));

?>