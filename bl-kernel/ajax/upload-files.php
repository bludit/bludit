<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

foreach ($_FILES['bluditInputFiles']['name'] as $key=>$filename) {

	// Clean filename and get extension
	$filename 	= Text::lowercase($filename);
	$fileExtension 	= pathinfo($filename, PATHINFO_EXTENSION);
	$filename 	= pathinfo($filename, PATHINFO_FILENAME);
	$filename 	= Text::replace(' ', '', $filename);
	$filename 	= Text::replace('_', '', $filename);

	// Generate the next filename if the filename already exist
	$tmpName = $filename.'.'.$fileExtension;
	if (Sanitize::pathFile(PATH_UPLOADS.$tmpName)) {
		$number = 0;
		$tmpName = $filename.'_'.$number.'.'.$fileExtension;
		while (Sanitize::pathFile(PATH_UPLOADS.$tmpName)) {
			$number++;
			$tmpName = $filename.'_'.$number.'.'.$fileExtension;
		}
	}

	// Move from temporary PHP folder to temporary Bludit folder
	$originalFile = PATH_TMP.'original'.'.'.$fileExtension;
	move_uploaded_file($_FILES['bluditInputFiles']['tmp_name'][$key], $originalFile);

	rename($originalFile, PATH_UPLOADS.$tmpName);
}

exit (json_encode(array(
	'status'=>0,
	'message'=>'Image uploaded success.',
	'filename'=>$tmpName
)));

?>