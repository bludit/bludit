<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// Type
$type = 'other';
if (!empty($_POST['type'])) {
	$type = Sanitize::html($_POST['type']);
}

// Filename and extension
$filename = Text::lowercase($_FILES['files']['name'][0]);
$fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
$filename = pathinfo($filename, PATHINFO_FILENAME);
$filename = Text::replace(' ', '', $filename);
$filename = Text::replace('_', '', $filename);

// Check extension
$validExtension = array('tiff', 'gif', 'png', 'jpg', 'jpeg', 'bmp', 'svg');
if (!in_array($fileExtension, $validExtension)) {
	$validExtensionString = implode(',', $validExtension);
	exit (json_encode(array(
		'status'=>1,
		'message'=>'Invalid extension file. Supported extensions:'.$validExtensionString
	)));
}

// Generate the next filename if the filename already exist
$tmpName = $filename.'.'.$fileExtension;
if (Sanitize::pathFile(PATH_UPLOADS.$tmpName)) {
	$number = 0;
	$tmpName = $filename.'_'.$number.'.'.$fileExtension;
	while (Sanitize::pathFile(PATH_UPLOADS.$tmpName)) {
		$number++;
		$tmpName = $filename.'_'.$number.'.'.$fileExtension;
	}
}

// Move from temporary PHP folder to temporary Bludit folder
$originalFile = PATH_TMP.'original'.'.'.$fileExtension;
move_uploaded_file($_FILES['files']['tmp_name'][0], $originalFile);

// Returned variables
$absoluteURL = '';
$absoluteURLThumbnail = '';
$absolutePath = '';

// --- PROFILE PICTURE ---
if ($type=='profilePicture') {
	// Resize and crop profile image
	$username = Sanitize::html($_POST['username']);
	$tmpName = $username.'.png';
	$Image = new Image();
	$Image->setImage($originalFile, PROFILE_IMG_WIDTH, PROFILE_IMG_HEIGHT, 'crop');
	$Image->saveImage(PATH_UPLOADS_PROFILES.$tmpName, PROFILE_IMG_QUALITY, false, true);

	// Paths
	$absoluteURL = DOMAIN_UPLOADS_PROFILES.$tmpName;
	$absoluteURLThumbnail = '';
	$absolutePath = PATH_UPLOADS_PROFILES.$tmpName;
}
// --- OTHERS ---
else {
	// Exclude generate thumbnail for SVG format
	if (strcasecmp($fileExtension, 'svg')!=0) {
		// Generate the thumbnail
		$Image = new Image();
		$Image->setImage($originalFile, THUMBNAILS_WIDTH, THUMBNAILS_HEIGHT, 'crop');
		$Image->saveImage(PATH_UPLOADS_THUMBNAILS.$tmpName, THUMBNAILS_QUALITY, true);
	}

	// Move the original to the upload folder
	rename($originalFile, PATH_UPLOADS.$tmpName);

	// Generate a link to the SVG file and save on thumbnails folder
	if (strcasecmp($fileExtension, 'svg')==0) {
		symlink(PATH_UPLOADS.$tmpName, PATH_UPLOADS_THUMBNAILS.$tmpName);
	}

	// Paths
	$absoluteURL = DOMAIN_UPLOADS.$tmpName;
	$absoluteURLThumbnail = DOMAIN_UPLOADS_THUMBNAILS.$tmpName;
	$absolutePath = PATH_UPLOADS.$tmpName;
}

// Remove the Bludit temporary file
if (Sanitize::pathFile($originalFile)) {
	unlink($originalFile);
}

exit (json_encode(array(
	'status'=>0,
	'message'=>'Image uploaded success.',
	'filename'=>$tmpName,
	'absoluteURL'=>$absoluteURL,
	'absoluteURLThumbnail'=>$absoluteURLThumbnail,
	'absolutePath'=>$absolutePath
)));

?>