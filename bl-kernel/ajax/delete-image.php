<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_POST
// ----------------------------------------------------------------------------
// (string) $_POST['path'] Name of file to delete, just the filename
$filename = isset($_POST['filename']) ? $_POST['filename'] : false;

// (string) $_POST['uuid']
$uuid = empty($_POST['uuid']) ? false : $_POST['uuid'];
// ----------------------------------------------------------------------------

if ($filename==false) {
	exit (json_encode(array(
		'status'=>1,
		'message'=>'The filename is empty.'
	)));
}

if ($uuid && IMAGE_RESTRICT) {
	$imagePath = PATH_UPLOADS_PAGES.$uuid.DS;
	$thumbnailPath = PATH_UPLOADS_PAGES.$uuid.DS.'thumbnails'.DS;
} else {
	$imagePath = PATH_UPLOADS;
	$thumbnailPath = PATH_UPLOADS_THUMBNAILS;
}

// Delete image
if (Sanitize::pathFile($imagePath.$filename)) {
	Filesystem::rmfile($imagePath.$filename);
}

// Delete thumbnail
if (Sanitize::pathFile($thumbnailPath.$filename)) {
	Filesystem::rmfile($thumbnailPath.$filename);
}

exit (json_encode(array(
	'status'=>0,
	'message'=>'Image deleted.'
)));

?>