<?php header('Content-Type: application/json');

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

move_uploaded_file($source, PATH_UPLOADS.$tmpName);

exit(json_encode(array(
	'status'=>0,
	'filename'=>$tmpName,
	'date'=>date("F d Y H:i:s.", filemtime(PATH_UPLOADS.$tmpName))
)));

?>