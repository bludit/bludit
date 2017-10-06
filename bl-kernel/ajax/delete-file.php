<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_POST
// (string) $filename: Name of file to delete, just the filename

$filename = isset($_POST['filename']) ? $_POST['filename'] : '';
if (Text::isEmpty($filename)) {
	exit (json_encode(array(
		'status'=>1,
		'message'=>'The filename is empty.'
	)));
}

// Check if the filename exist
if (!Sanitize::pathFile(PATH_UPLOADS.$filename)) {
	exit (json_encode(array(
		'status'=>1,
		'message'=>'The file does not exist.'
	)));
}
// Delete the file
Filesystem::rmfile(PATH_UPLOADS.$filename);

// Check if the file has a thumbnail
if (Sanitize::pathFile(PATH_UPLOADS_THUMBNAILS.$filename)) {
	// Delete the file
	Filesystem::rmfile(PATH_UPLOADS_THUMBNAILS.$filename);
}

exit (json_encode(array(
	'status'=>0,
	'message'=>'File deleted.'
)));

?>