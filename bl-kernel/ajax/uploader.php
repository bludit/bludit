<?php defined('BLUDIT') or die('Bludit CMS.');

header('Content-Type: application/json');

// Type
$type = 'other';
if(!empty($_POST['type'])) {
	$type = Sanitize::html($_POST['type']);
}

// Source.
$source = $_FILES['files']['tmp_name'][0];

// Filename and extension.
$filename = Text::lowercase($_FILES['files']['name'][0]);
$fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
$filename = pathinfo($filename, PATHINFO_FILENAME);
$filename = Text::replace(' ', '', $filename);
$filename = Text::replace('_', '', $filename);

// Generate the next filename if the filename already exist.
$tmpName = $filename.'.'.$fileExtension;
if( file_exists(PATH_UPLOADS.$tmpName) )
{
	$number = 0;
	$tmpName = $filename.'_'.$number.'.'.$fileExtension;
	while(file_exists(PATH_UPLOADS.$tmpName)) {
		$number++;
		$tmpName = $filename.'_'.$number.'.'.$fileExtension;
	}
}

// Move from temporary PHP folder to temporary Bludit folder.
move_uploaded_file($source, PATH_TMP.'original'.'.'.$fileExtension);

// --- PROFILE PICTURE ---
if($type=='profilePicture')
{
	// Resize and crop profile image.
	$username = Sanitize::html($_POST['username']);
	$tmpName = $username.'.png';
	$Image = new Image();
	$Image->setImage(PATH_TMP.'original'.'.'.$fileExtension, '400', '400', 'crop');
	$Image->saveImage(PATH_UPLOADS_PROFILES.$tmpName, 100, false, true);
}
// --- OTHERS ---
else {
	// Generate the thumbnail
	$Image = new Image();
	$Image->setImage(PATH_TMP.'original'.'.'.$fileExtension, THUMBNAILS_WIDTH, THUMBNAILS_HEIGHT, 'crop');
	$Image->saveImage(PATH_UPLOADS_THUMBNAILS.$tmpName, 100, true);

	// Move the original to the upload folder.
	rename(PATH_TMP.'original'.'.'.$fileExtension, PATH_UPLOADS.$tmpName);
}

// Remove the Bludit temporary file.
if(file_exists(PATH_TMP.'original'.'.'.$fileExtension)) {
	unlink(PATH_TMP.'original'.'.'.$fileExtension);
}

exit(json_encode(array(
	'status'=>0,
	'filename'=>$tmpName
)));

?>