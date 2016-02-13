<?php defined('BLUDIT') or die('Bludit CMS.');

header('Content-Type: application/json');

// Request $_POST
// $filename: Name of file to delete, just the filename

$filename = isset($_POST['filename']) ? $_POST['filename'] : '';

if( empty($filename) ) {
	echo json_encode( array('status'=>0, 'msg'=>'The filename is empty.') );
	exit;
}

// Check if the filename exist and Sanitize::pathFile it's necesary for security reasons.
if( Sanitize::pathFile(PATH_UPLOADS.$filename) ) {

	// Delete the file.
	Filesystem::rmfile(PATH_UPLOADS.$filename);

	// Delete the thumnails.
	Filesystem::rmfile(PATH_UPLOADS_THUMBNAILS.$filename);

	echo json_encode( array('status'=>1, 'msg'=>'The file was deleted.') );

	exit;
}

echo json_encode( array('status'=>0, 'msg'=>'The file does not exist.') );

?>