<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

// Delete logo image
$logoFilename = $site->logo(false);
if ($logoFilename) {
	Filesystem::rmfile(PATH_UPLOADS.$logoFilename);
}

// Remove the logo from the database
$site->set(array('logo'=>''));

ajaxResponse(0, 'Logo removed.');

?>