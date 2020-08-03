<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Upload site logo
| The final filename is the site's name and the extension is the same as the file uploaded
|
| @_FILES['inputFile']	multipart/form-data	File from form
|
| @return	array
*/

if (!isset($_FILES['inputFile'])) {
	ajaxResponse(1, 'Error trying to upload the site logo.');
}

// Check path traversal on $filename
if (Text::stringContains($_FILES['inputFile']['name'], DS, false)) {
	$message = 'Path traversal detected.';
	Log::set($message, LOG_TYPE_ERROR);
	ajaxResponse(1, $message);
}

// File extension
$fileExtension = Filesystem::extension($_FILES['inputFile']['name']);
$fileExtension = Text::lowercase($fileExtension);
if (!in_array($fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSION'])) {
	$message = $L->g('File type is not supported. Allowed types:').' '.implode(', ',$GLOBALS['ALLOWED_IMG_EXTENSION']);
	Log::set($message, LOG_TYPE_ERROR);
	ajaxResponse(1, $message);
}

// File MIME Type
$fileMimeType = Filesystem::mimeType($_FILES['inputFile']['tmp_name']);
if ($fileMimeType!==false) {
	if (!in_array($fileMimeType, $GLOBALS['ALLOWED_IMG_MIMETYPES'])) {
		$message = $L->g('File mime type is not supported. Allowed types:').' '.implode(', ',$GLOBALS['ALLOWED_IMG_MIMETYPES']);
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}
}

// Final filename
$filename = 'logo.'.$fileExtension;
if (Text::isNotEmpty( $site->title() )) {
	$filename = $site->title().'.'.$fileExtension;
}

// Delete old image
$oldFilename = $site->logo(false);
if ($oldFilename) {
	Filesystem::rmfile(PATH_UPLOADS.$oldFilename);
}

// Move from temporary directory to uploads
Filesystem::mv($_FILES['inputFile']['tmp_name'], PATH_UPLOADS.$filename);

// Permissions
chmod(PATH_UPLOADS.$filename, 0644);

// Store the filename in the database
$site->set(array('logo'=>$filename));

ajaxResponse(0, 'Image uploaded.', array(
	'filename'=>$filename,
	'absoluteURL'=>DOMAIN_UPLOADS.$filename,
	'absolutePath'=>PATH_UPLOADS.$filename
));

?>
