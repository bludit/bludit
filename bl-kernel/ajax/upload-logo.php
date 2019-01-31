<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

if (!isset($_FILES['inputFile'])) {
	ajaxResponse(1, 'Error trying to upload the site logo.');
}

// File extension
$fileExtension = pathinfo($_FILES['inputFile']['name'], PATHINFO_EXTENSION);

// Final filename
$filename = 'logo.'.$fileExtension;
if (Text::isNotEmpty( $site->title() )) {
	$filename = $site->title().'.'.$fileExtension;
}

// Delete old image
$oldFilename = $site->logo(false);
if ($oldFilename) {
	Filesystem::rmfile(PATH_UPLOADS.$oldFilename);
}

// Move from temporary directory to uploads
rename($_FILES['inputFile']['tmp_name'], PATH_UPLOADS.$filename);

// Permissions
chmod(PATH_UPLOADS.$filename, 0644);

// Store the filename in the database
$site->set(array('logo'=>$filename));

ajaxResponse(0, 'Image uploaded.', array(
	'filename'=>$filename,
	'absoluteURL'=>DOMAIN_UPLOADS.$filename,
	'absolutePath'=>PATH_UPLOADS.$filename
));

?>