<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// $_POST
// ----------------------------------------------------------------------------
// (string) $_POST['username']
$username = empty($_POST['username']) ? false : $_POST['username'];
// ----------------------------------------------------------------------------

if ($username===false) {
	ajaxResponse(1, 'Error in username.');
}

if ( ($login->role()!='admin') && ($login->username()!=$username) ) {
	ajaxResponse(1, 'Error in username.');
}

if (!isset($_FILES['profilePictureInputFile'])) {
	ajaxResponse(1, 'Error trying to upload the profile picture.');
}

// Check path traversal
if (Text::stringContains($username, DS, false)) {
	$message = 'Path traversal detected.';
	Log::set($message, LOG_TYPE_ERROR);
	ajaxResponse(1, $message);
}

// Sanitize username for filename to prevent issues with special characters
$sanitizedUsername = Text::removeSpecialCharacters($username, '-');
$sanitizedUsername = Text::removeQuotes($sanitizedUsername);
$sanitizedUsername = Text::removeSpaces($sanitizedUsername, '-');

// Check file extension
$fileExtension = Filesystem::extension($_FILES['profilePictureInputFile']['name']);
$fileExtension = Text::lowercase($fileExtension);
if (!in_array($fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSION']) ) {
	$message = $L->g('File type is not supported. Allowed types:').' '.implode(', ',$GLOBALS['ALLOWED_IMG_EXTENSION']);
	Log::set($message, LOG_TYPE_ERROR);
	ajaxResponse(1, $message);
}

// Check file MIME Type
$fileMimeType = Filesystem::mimeType($_FILES['profilePictureInputFile']['tmp_name']);
if ($fileMimeType!==false) {
	if (!in_array($fileMimeType, $GLOBALS['ALLOWED_IMG_MIMETYPES'])) {
		$message = $L->g('File mime type is not supported. Allowed types:').' '.implode(', ',$GLOBALS['ALLOWED_IMG_MIMETYPES']);
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}
}

// Tmp filename
$tmpFilename = $sanitizedUsername.'.'.$fileExtension;

// Final filename
$filename = $sanitizedUsername.'.png';

// Ensure Bludit tmp directory exists
if (!Filesystem::directoryExists(PATH_TMP)) {
	if (!Filesystem::mkdir(PATH_TMP, true)) {
		$message = 'Temporary directory does not exist and cannot be created.';
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}
}

// Move from temporary directory to uploads folder
$moved = rename($_FILES['profilePictureInputFile']['tmp_name'], PATH_TMP.$tmpFilename);
if (!$moved) {
	$message = 'Error moving uploaded file to temporary directory.';
	Log::set($message, LOG_TYPE_ERROR);
	ajaxResponse(1, $message);
}

// Resize and convert to png
$image = new Image();
$image->setImage(PATH_TMP.$tmpFilename, PROFILE_IMG_WIDTH, PROFILE_IMG_HEIGHT, 'crop');
$image->saveImage(PATH_UPLOADS_PROFILES.$filename, PROFILE_IMG_QUALITY, false, true);

// Delete temporary file
Filesystem::rmfile(PATH_TMP.$tmpFilename);

// Permissions
chmod(PATH_UPLOADS_PROFILES.$filename, 0644);

ajaxResponse(0, 'Image uploaded.', array(
	'filename'=>$filename,
	'absoluteURL'=>DOMAIN_UPLOADS_PROFILES.$filename,
	'absolutePath'=>PATH_UPLOADS_PROFILES.$filename
));

?>
