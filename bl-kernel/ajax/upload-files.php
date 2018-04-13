<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

foreach ($_FILES['bluditInputFiles']['name'] as $key=>$filename) {
	// Get the next filename if already exist the file to not overwrite the original file
	$nextFilename = Filesystem::nextFilename(PATH_UPLOADS, $filename);

	// File extension
	$fileExtension 	= pathinfo($nextFilename, PATHINFO_EXTENSION);

	// Move from temporary directory to uploads folder
	rename($_FILES['bluditInputFiles']['tmp_name'][$key], PATH_UPLOADS.$nextFilename);

	// Generate Thumbnail

	// Exclude generate thumbnail for SVG format and generate a symlink to the svg
	if ($fileExtension == 'svg') {
		symlink(PATH_UPLOADS.$nextFilename, PATH_UPLOADS_THUMBNAILS.$nextFilename);
	} else {
		$Image = new Image();
		$Image->setImage(PATH_UPLOADS.$nextFilename, $GLOBALS['THUMBNAILS_WIDTH'], $GLOBALS['THUMBNAILS_HEIGHT'], 'crop');
		$Image->saveImage(PATH_UPLOADS_THUMBNAILS.$nextFilename, $GLOBALS['THUMBNAILS_QUALITY'], true);
	}
}

$absoluteURL 		= DOMAIN_UPLOADS.$nextFilename;
$absoluteURLThumbnail 	= DOMAIN_UPLOADS_THUMBNAILS.$nextFilename;
$absolutePath 		= PATH_UPLOADS.$nextFilename;

exit (json_encode(array(
	'status'=>0,
	'message'=>'Image uploaded success.',
	'filename'=>$nextFilename,
	'absoluteURL'=>$absoluteURL,
	'absoluteURLThumbnail'=>$absoluteURLThumbnail,
	'absolutePath'=>$absolutePath
)));

?>