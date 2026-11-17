<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Returns a list of images from a particular page
|
| @_POST['pageNumber']	int	Page number for the paginator
| @_POST['path']	string	Pre-defined name for the directory to read, its pre-defined to avoid security issues
| @_POST['uuid']	string	Page UUID
|
| @return	array	Each file is an object with 'filename' (original),
|			'thumbnail' (resolved preview filename — may differ from
|			the original for legacy pairs or fall back to it when no
|			thumbnail exists), 'alt', 'width', 'height', 'bytes' and
|			'mime'.
*/

// $_POST
// ----------------------------------------------------------------------------
// $_POST['pageNumber'] > 0
$pageNumber = empty($_POST['pageNumber']) ? 1 : (int)$_POST['pageNumber'];
$pageNumber = $pageNumber - 1;

$path = empty($_POST['path']) ? false : $_POST['path'];
$uuid = empty($_POST['uuid']) ? false : $_POST['uuid'];
// ----------------------------------------------------------------------------

// The only accepted value is kept for backward-compat with clients that
// preserve the old contract; the server now scans originals regardless.
if ($path !== 'thumbnails') {
	ajaxResponse(1, 'Invalid path.');
}

// Resolve the originals and thumbnails directories
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

// Scan originals and pair each with its thumbnail
$list = mediaManagerListImages($imagePath, $thumbnailPath, MEDIA_MANAGER_NUMBER_OF_FILES, $uuid, $pageNumber + 1);

ajaxResponse(0, 'List of files and number of chunks.', array(
	'numberOfPages'=>$list['numberOfPages'],
	'files'=>$list['files']
));

?>
