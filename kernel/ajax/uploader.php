<?php defined('BLUDIT') or die('Bludit CMS.');

header('Content-Type: application/json');

// Type
$type = 'other';
if(!empty($_POST['type'])) {
	$type = Sanitize::html($_POST['type']);
}

// Source
$source = $_FILES['files']['tmp_name'][0];

// Filename
$filename = Text::lowercase($_FILES['files']['name'][0]);
$fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
$filename = pathinfo($filename, PATHINFO_FILENAME);
$filename = Text::replace(' ', '', $filename);
$filename = Text::replace('_', '', $filename);

if( file_exists(PATH_UPLOADS.$filename.'.'.$fileExtension) )
{
	$number = 0;
	$tmpName = $filename.'_'.$number.'.'.$fileExtension;
	while(file_exists(PATH_UPLOADS.$tmpName)) {
		$number++;
		$tmpName = $filename.'_'.$number.'.'.$fileExtension;
	}
}

if(empty($tmpName)) {
	$tmpName = $filename.'.'.$fileExtension;
}

// --- PROFILE PICTURE ---
if($type=='profilePicture') {
	$username = Sanitize::html($_POST['username']);
	$tmpName = $username.'.jpg';

	move_uploaded_file($source, PATH_UPLOADS_PROFILES.$tmpName);

	// Resize and crop profile image.
	$Image = new Image();
	$Image->setImage(PATH_UPLOADS_PROFILES.$tmpName, '200', '200', 'crop');
	$Image->saveImage(PATH_UPLOADS_PROFILES.$tmpName, 100, true);
}
// --- OTHERS ---
else {
	move_uploaded_file($source, PATH_UPLOADS.$tmpName);
}

exit(json_encode(array(
	'status'=>0,
	'filename'=>$tmpName
)));

?>