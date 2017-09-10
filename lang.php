<?php

$string = file_get_contents("bl-languages/en.json");
$english = json_decode($string, true);

$string = file_get_contents("bl-languages/ja_JP.json");
$spanish = json_decode($string, true);

$tmp = array();
foreach($english as $key=>$value) {
	if (isset($spanish[$key])) {
		$tmp[$key] = $spanish[$key];
	} else {
		$tmp[$key] = $value;
	}
}

echo json_encode($tmp, JSON_UNESCAPED_UNICODE);