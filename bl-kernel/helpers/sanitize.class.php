<?php defined('BLUDIT') or die('Bludit CMS.');

class Sanitize {

	public static function removeTags($text) {
		return strip_tags($text);
	}

	// Convert special characters to HTML entities
	public static function html($text)
	{
		$flags = ENT_COMPAT;

		if (defined('ENT_HTML5')) {
			$flags = ENT_COMPAT|ENT_HTML5;
		}

		return htmlspecialchars($text, $flags, CHARSET);
	}

	// Convert special HTML entities back to characters
	public static function htmlDecode($text)
	{
		$flags = ENT_COMPAT;

		if(defined('ENT_HTML5')) {
			$flags = ENT_COMPAT|ENT_HTML5;
		}

		return htmlspecialchars_decode($text, $flags);
	}

	public static function pathFile($path, $file=false)
	{
		if ($file!==false){
			$fullPath = $path.$file;
		} else {
			$fullPath = $path;
		}

		// Fix for Windows on paths. eg: $path = c:\diego/page/subpage convert to c:\diego\page\subpages
		$fullPath = str_replace('/', DS, $fullPath);

		if (CHECK_SYMBOLIC_LINKS) {
			$real = realpath($fullPath);
		} else {
			$real = file_exists($fullPath)?$fullPath:false;
		}

		// If $real is FALSE the file does not exist.
		if ($real===false) {
			return false;
		}

		// If the $real path does not start with the systemPath then this is Path Traversal.
		if (strpos($fullPath, $real)!==0) {
			return false;
		}

		return true;
	}

	// Returns the email without illegal characters.
	public static function email($email)
	{
		return( filter_var($email, FILTER_SANITIZE_EMAIL) );
	}

	public static function url($url)
	{
		return( filter_var($url, FILTER_SANITIZE_URL) );
	}

	public static function int($value)
	{
		$value = (int)$value;

		if($value>=0)
			return $value;
		else
			return 0;
	}

}