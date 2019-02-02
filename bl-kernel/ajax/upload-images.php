<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_POST
// ----------------------------------------------------------------------------
// (string) $_POST['uuid']
$uuid = empty($_POST['uuid']) ? false : $_POST['uuid'];
// ----------------------------------------------------------------------------

// Set upload directory
if ($uuid && IMAGE_RESTRICT) {
	$uploadDirectory = PATH_UPLOADS_PAGES.$uuid.DS;
	$thumbnailDirectory = $uploadDirectory.'thumbnails'.DS;
} else {
	$uploadDirectory = PATH_UPLOADS;
	$thumbnailDirectory = PATH_UPLOADS_THUMBNAILS;
}

// Create directory for images
if (!is_dir($uploadDirectory)){
	Filesystem::mkdir($uploadDirectory, true);
}

// Create directory for thumbnails
if (!is_dir($thumbnailDirectory)){
	Filesystem::mkdir($thumbnailDirectory, true);
}

// File extensions allowed
$allowedExtensions =  array('gif', 'png', 'jpg', 'jpeg', 'svg');

// Upload all images
foreach ($_FILES['bluditInputFiles']['name'] as $key=>$filename) {

	// Check for errors
	if ($_FILES['bluditInputFiles']['error'][$key] != 0) {
		$message = $L->g('Maximum load file size allowed:').' '.ini_get('upload_max_filesize');
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}

	// Convert URL characters such as spaces or quotes to characters
	$filename = urldecode($filename);

	// Check file extension
	$fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
	$fileExtension = Text::lowercase($fileExtension);
	if (!in_array($fileExtension, $allowedExtensions) ) {
		$message = $L->g('File type is not supported. Allowed types:').' '.implode(', ',$allowedExtensions);
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}

	// Generate the next filename to not overwrite the original file
	$nextFilename = Filesystem::nextFilename($uploadDirectory, $filename);

	// Move from temporary directory to uploads folder
	rename($_FILES['bluditInputFiles']['tmp_name'][$key], $uploadDirectory.$nextFilename);
	chmod($uploadDirectory.$nextFilename, 0644);

	// Generate Thumbnail
	// Exclude generate thumbnail for SVG format and generate a symlink to the svg
	if ($fileExtension == 'svg') {
		symlink($uploadDirectory.$nextFilename, $thumbnailDirectory.$nextFilename);
	} else {
		$Image = new Image();
		$Image->setImage($uploadDirectory.$nextFilename, $site->thumbnailWidth(), $site->thumbnailHeight(), 'crop');
		$Image->saveImage($thumbnailDirectory.$nextFilename, $site->thumbnailQuality(), true);
	}
}

ajaxResponse(0, 'List of files and number of chunks.', array(
	'filename'=>$nextFilename
));

?>