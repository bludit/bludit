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

	/*
		Check if the path exists, also check for path traversal.

		@path		string	The path to check, could be a path with a file

		@returns	boolean	Returns TRUE if the path exists and is not path traversal, FALSE otherwise
	*/
	public static function pathFile($path)
	{
		// Fix for Windows on paths. eg: $path = c:\diego/page/subpage convert to c:\diego\page\subpages
		$fullPath = str_replace('/', DS, $path);

		if (CHECK_SYMBOLIC_LINKS) {
			$real = realpath($fullPath);
		} else {
			$real = file_exists($fullPath)?$fullPath:false;
		}

		// If $real is FALSE the path doesn't exist
		if ($real===false) {
			return false;
		}

		// If the $real path doesn't start with the systemPath then this is Path Traversal
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