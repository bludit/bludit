<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// Options allowed for this AJAX
$optionsAllowed = array(
	'profilePicture',
	'siteLogo',
	'favicon'
);

// Image transformation
$transformation = false;

// $_POST
// ----------------------------------------------------------------------------

// (string) $_POST['option']
$option = empty($_POST['option']) ? false : $_POST['option'];
if (!in_array($option, $optionsAllowed)) {
	exit (json_encode(array(
		'status'=>1,
		'message'=>'Error in option parameter.'
	)));
}

// (string) $_POST['username']
if ($option=='profilePicture') {
	$username = empty($_POST['username']) ? false : $_POST['username'];
	if ($username===false) {
		exit (json_encode(array(
			'status'=>1,
			'message'=>'Error in username parameter.'
		)));
	}

	$transformation = array(
		'resize'=>true,
		'width'=>PROFILE_IMG_WIDTH,
		'height'=>PROFILE_IMG_HEIGHT,
		'quality'=>PROFILE_IMG_QUALITY,
		'forceJPG'=>false,
		'forcePNG'=>true,
		'option'=>'crop'
	);

	$finalPath = PATH_UPLOADS_PROFILES;
	$finalFilename = $username.'png';
} elseif ($option=='siteLogo') {
	$finalPath = PATH_UPLOADS;
	$finalFilename = 'logo';
}

// ----------------------------------------------------------------------------

if (!isset($_FILES['inputFile'])) {
	exit (json_encode(array(
		'status'=>1,
		'message'=>'Error trying to upload the file.'
	)));
}

// File extension
$fileExtension 	= pathinfo($_FILES['inputFile']['name'], PATHINFO_EXTENSION);

// Tmp filename
$tmpFilename = 'tmp.'.$fileExtension;

// Move from temporary directory to uploads folder
rename($_FILES['inputFile']['tmp_name'], PATH_TMP.$tmpFilename);

// Resize and crop image
if ($transformation['resize']) {
	$image = new Image();
	$image->setImage(PATH_TMP.$tmpFilename, $transformation['width'], $transformation['heigh'], $transformation['option']);
	$image->saveImage($finalPath.$finalFilename, $transformation['quality'], $transformation['forceJPG'], $transformation['forcePNG']);
} else {
	rename(PATH_TMP.$tmpFilename, $finalPath.$finalFilename.'.'.$fileExtension);
}

// Remove the tmp file
unlink(PATH_TMP.$tmpFilename);

// Permissions
chmod($finalPath.$finalFilename, 0644);

exit (json_encode(array(
	'status'=>0,
	'message'=>'Image uploaded success.',
	'filename'=>$finalPath.$finalFilename
)));

?>