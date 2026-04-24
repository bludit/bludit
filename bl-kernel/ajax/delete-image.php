<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Delete an image from a particular page
|
| @_POST['filename']	string	Name of the file to delete
| @_POST['uuid']	string	Page UUID
|
| @return	array
*/

// $_POST
// ----------------------------------------------------------------------------
$filename = isset($_POST['filename']) ? $_POST['filename'] : false;
$uuid = empty($_POST['uuid']) ? false : $_POST['uuid'];
// ----------------------------------------------------------------------------

if ($filename===false) {
	ajaxResponse(1, 'The filename is empty.');
}

if ($uuid && IMAGE_RESTRICT) {
	if (Text::stringContains($uuid, DS, false)) {
		ajaxResponse(1, 'Invalid uuid.');
	}
	$imagePath = PATH_UPLOADS_PAGES.$uuid.DS;
	$thumbnailPath = PATH_UPLOADS_PAGES.$uuid.DS.'thumbnails'.DS;
} else {
	$imagePath = PATH_UPLOADS;
	$thumbnailPath = PATH_UPLOADS_THUMBNAILS;
}

// Delete the original
if (Sanitize::pathFile($imagePath.$filename)) {
	Filesystem::rmfile($imagePath.$filename);
}

// Delete the thumbnail. Exact-name match is the fast path (new uploads have
// matching extensions). If no exact match, fall back to any allowed-extension
// match on the basename — this recovers legacy pairs where thumbnails were
// forced to .jpg while the original kept its real extension. Before deleting
// a mismatched candidate, verify no other original owns that extension, to
// avoid taking out an unrelated image's thumbnail.
if (Sanitize::pathFile($thumbnailPath.$filename) && is_file($thumbnailPath.$filename)) {
	Filesystem::rmfile($thumbnailPath.$filename);
} else {
	$base = pathinfo($filename, PATHINFO_FILENAME);
	foreach ($GLOBALS['ALLOWED_IMG_EXTENSION'] as $ext) {
		$candidate = $base.'.'.$ext;
		if ($candidate === $filename) {
			continue;
		}
		if (is_file($imagePath.$candidate)) {
			continue;
		}
		if (Sanitize::pathFile($thumbnailPath.$candidate) && is_file($thumbnailPath.$candidate)) {
			Filesystem::rmfile($thumbnailPath.$candidate);
		}
	}
}

ajaxResponse(0, 'Image deleted.');

?>
