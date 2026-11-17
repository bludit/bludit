<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Save the metadata of an image
|
| @_POST['filename']	string	Name of the file
| @_POST['alt']		string	Alt text
| @_POST['uuid']	string	Page UUID
|
| @return	array
*/

// $_POST
// ----------------------------------------------------------------------------
$filename = isset($_POST['filename']) ? basename($_POST['filename']) : false;
$uuid = empty($_POST['uuid']) ? false : $_POST['uuid'];
$alt = isset($_POST['alt']) ? (string) $_POST['alt'] : '';
// ----------------------------------------------------------------------------

if ($filename === false || $filename === '') {
	ajaxResponse(1, 'The filename is empty.');
}

if ($uuid && IMAGE_RESTRICT) {
	if (!$pages->isValidUUID($uuid)) {
		ajaxResponse(1, 'Invalid uuid.');
	}
	if (!canManagePageImages($uuid)) {
		ajaxResponse(1, $L->g('You do not have sufficient permissions'));
	}
	$imagePath = PATH_UPLOADS_PAGES.$uuid.DS;
} else {
	$imagePath = PATH_UPLOADS;
}

// The metadata can only be saved for files that exist in the page directory
if (!Sanitize::pathFile($imagePath, $filename) || !is_file($imagePath.$filename)) {
	ajaxResponse(1, 'The image does not exist.');
}

// Alt text is plain text. Remove tags and control characters, and keep it at
// a reasonable length.
$alt = trim(strip_tags($alt));
$alt = preg_replace('/[\x00-\x1F\x7F]/u', '', $alt);
if ($alt === null) {
	$alt = '';
}
$alt = function_exists('mb_substr') ? mb_substr($alt, 0, 500, CHARSET) : substr($alt, 0, 500);

$metadata = mediaManagerMetadataGet($uuid, $filename);
$metadata['alt'] = $alt;

if (!mediaManagerMetadataSave($uuid, $filename, $metadata)) {
	ajaxResponse(1, 'Error saving the image metadata.');
}

ajaxResponse(0, 'Image metadata saved.', array(
	'alt' => $alt
));

?>
