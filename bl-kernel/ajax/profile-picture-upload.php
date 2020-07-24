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
$tmpFilename = $username.'.'.$fileExtension;

// Final filename
$filename = $username.'.png';

// Move from temporary directory to uploads folder
rename($_FILES['profilePictureInputFile']['tmp_name'], PATH_TMP.$tmpFilename);

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
