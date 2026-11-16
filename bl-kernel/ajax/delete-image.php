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
$filename = isset($_POST['filename']) ? basename($_POST['filename']) : false;
$uuid = empty($_POST['uuid']) ? false : $_POST['uuid'];
// ----------------------------------------------------------------------------

if ($filename===false || $filename==='') {
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
	$thumbnailPath = PATH_UPLOADS_PAGES.$uuid.DS.'thumbnails'.DS;
} else {
	$imagePath = PATH_UPLOADS;
	$thumbnailPath = PATH_UPLOADS_THUMBNAILS;
}

// Delete the original
if (Sanitize::pathFile($imagePath, $filename)) {
	Filesystem::rmfile($imagePath.$filename);
}

// Remove the metadata (alt text and cached properties) for the image
mediaManagerMetadataDelete($uuid, $filename);

// Delete the thumbnail. Exact-name match is the fast path (new uploads have
// matching extensions). If no exact match, fall back to any allowed-extension
// match on the basename — this recovers legacy pairs where thumbnails were
// forced to .jpg while the original kept its real extension. Before deleting
// a mismatched candidate, verify no other original owns that extension, to
// avoid taking out an unrelated image's thumbnail.
// SVG thumbnails are symlinks to the original, so they must be checked with
// is_link() and removed with unlink(). Sanitize::pathFile() can't be used here
// because it resolves symlinks (CHECK_SYMBOLIC_LINKS) and rejects a link that
// points outside the thumbnails directory. $filename is already a basename()
// and only the directory entry itself is removed, never the link target.
$thumbnailEntry = function ($file) use ($thumbnailPath) {
	return is_file($thumbnailPath.$file) || is_link($thumbnailPath.$file);
};

if ($thumbnailEntry($filename)) {
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
		if ($thumbnailEntry($candidate)) {
			Filesystem::rmfile($thumbnailPath.$candidate);
		}
	}
}

ajaxResponse(0, 'Image deleted.');

?>
