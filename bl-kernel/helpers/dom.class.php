<?php defined('BLUDIT') or die('Bludit CMS.');

class DOM {

	public static function getFirstImage($content)
	{
		// Disable warning
		libxml_use_internal_errors(true);
		$dom = new DOMDocument();
		$dom->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$content);
		$finder = new DomXPath($dom);

		$images = $finder->query("//img");

		if($images->length>0) {
			// First image from the list
			$image = $images->item(0);
			// Get value from attribute src
			$imgSrc = $image->getAttribute('src');
			// Returns the image src
			return $imgSrc;
		}

		return false;
	}

}