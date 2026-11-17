<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Replace an image keeping its filename, so URLs already used in the content
| and the cover image keep working.
|
| @_POST['filename']	string	Name of the file to replace
| @_POST['uuid']	string	Page UUID
| @_FILES['image']	file	New image, same extension as the current file
|
| @return	array
*/

// $_POST
// ----------------------------------------------------------------------------
$filename = isset($_POST['filename']) ? basename($_POST['filename']) : false;
$uuid = empty($_POST['uuid']) ? false : $_POST['uuid'];
// ----------------------------------------------------------------------------

if ($filename === false || $filename === '') {
	ajaxResponse(1, 'The filename is empty.');
}

if (!isset($_FILES['image']) || !isset($_FILES['image']['error'])) {
	ajaxResponse(1, 'No image was uploaded.');
}

if ($_FILES['image']['error'] != 0) {
	$message = $L->g('Maximum load file size allowed:') . ' ' . ini_get('upload_max_filesize');
	Log::set($message, LOG_TYPE_ERROR);
	ajaxResponse(1, $message);
}

if ($uuid && IMAGE_RESTRICT) {
	if (!$pages->isValidUUID($uuid)) {
		ajaxResponse(1, 'Invalid uuid.');
	}
	if (!canManagePageImages($uuid)) {
		ajaxResponse(1, $L->g('You do not have sufficient permissions'));
	}
	$imageDirectory = PATH_UPLOADS_PAGES . $uuid . DS;
	$thumbnailDirectory = $imageDirectory . 'thumbnails' . DS;
} else {
	$imageDirectory = PATH_UPLOADS;
	$thumbnailDirectory = PATH_UPLOADS_THUMBNAILS;
}

if (!Sanitize::pathFile($imageDirectory, $filename) || !is_file($imageDirectory . $filename)) {
	ajaxResponse(1, 'The image does not exist.');
}

// The replacement keeps the filename, so it must have the same extension.
$targetExtension = Text::lowercase(Filesystem::extension($filename));
$fileExtension = Text::lowercase(Filesystem::extension($_FILES['image']['name']));
if (!in_array($targetExtension, $GLOBALS['ALLOWED_IMG_EXTENSION'])) {
	$message = $L->g('File type is not supported. Allowed types:') . ' ' . implode(', ', $GLOBALS['ALLOWED_IMG_EXTENSION']);
	ajaxResponse(1, $message);
}
if ($fileExtension !== $targetExtension) {
	ajaxResponse(1, 'The replacement must be a .' . $targetExtension . ' file.');
}

// Check the MIME type of the uploaded file
$fileMimeType = Filesystem::mimeType($_FILES['image']['tmp_name']);
if ($fileMimeType === false || !in_array($fileMimeType, $GLOBALS['ALLOWED_IMG_MIMETYPES'])) {
	$message = $L->g('File mime type is not supported. Allowed types:') . ' ' . implode(', ', $GLOBALS['ALLOWED_IMG_MIMETYPES']);
	Log::set($message, LOG_TYPE_ERROR);
	ajaxResponse(1, $message);
}

// Ensure Bludit tmp directory exists
if (!Filesystem::directoryExists(PATH_TMP)) {
	if (!Filesystem::mkdir(PATH_TMP, true)) {
		$message = 'Temporary directory does not exist and cannot be created.';
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}
}

// Move the upload to a random temporary name, the user filename is not used.
$tmpFile = PATH_TMP . 'media-replace-' . uniqid() . '.' . $targetExtension;
if (!Filesystem::mv($_FILES['image']['tmp_name'], $tmpFile)) {
	$message = 'Error moving uploaded file to temporary directory.';
	Log::set($message, LOG_TYPE_ERROR);
	ajaxResponse(1, $message);
}

$image = replaceImage($tmpFile, $imageDirectory, $thumbnailDirectory, $filename);
if (!$image) {
	$message = 'Error replacing the image.';
	Log::set($message, LOG_TYPE_ERROR);
	ajaxResponse(1, $message);
}

// Refresh the cached properties, keep the alt text.
$metadata = mediaManagerMetadataGet($uuid, $filename);
$bytes = @filesize($image);
$metadata['bytes'] = ($bytes === false) ? 0 : (int) $bytes;
$info = @getimagesize($image);
if ($info !== false) {
	$metadata['width'] = (int) $info[0];
	$metadata['height'] = (int) $info[1];
	if (!empty($info['mime'])) {
		$metadata['mime'] = $info['mime'];
	}
} else {
	$metadata['mime'] = ($fileMimeType === false) ? '' : $fileMimeType;
}
mediaManagerMetadataSave($uuid, $filename, $metadata);

ajaxResponse(0, 'Image replaced.', array(
	'filename' => $filename
));

?>
