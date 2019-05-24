<?php

$string = file_get_contents("bl-languages/en.json");
$english = json_decode($string, true);

$files = glob('bl-languages/*.{json}', GLOB_BRACE);
foreach ($files as $file) {
	$out = $file;
	$string = file_get_contents($out);
	$spanish = json_decode($string, true);

	$tmp = array();
	$tmp['language-data'] 	= $spanish['language-data'];
	$tmp['dates'] 		= $spanish['dates'];
	if (isset($spanish['special-chars'])) {
		$tmp['special-chars'] 	= $spanish['special-chars'];
	}

	foreach ($english as $key=>$value) {
		if (isset($spanish[$key])) {
			$tmp[$key] = $spanish[$key];
		} else {
			$tmp[$key] = $value;
		}
	}

	$json = json_encode($tmp, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	file_put_contents($out, $json);
}
