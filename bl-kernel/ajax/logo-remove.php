<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Delete the site logo
| This script delete the file and set and empty string in the database
|
| @return	array
*/

// Delete the file
$logoFilename = $site->logo(false);
if ($logoFilename) {
	Filesystem::rmfile(PATH_UPLOADS.$logoFilename);
}

// Remove the logo from the database
$site->set(array('logo'=>''));

ajaxResponse(0, 'Logo removed.');

?>