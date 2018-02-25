<?php
/*
	This file is loaded before the theme
	You can add some configuration code in this file
*/

// Background image
$backgroundImage = 'https://source.unsplash.com/1600x900/?nature';
if ($page->coverImage()===false) {
	$domImage = DOM::getFirstImage($page->content($fullContent=true));
	if ($domImage!==false) {
		$backgroundImage = $domImage;
	}
} else {
	$backgroundImage = $page->coverImage($absolute=true);
}