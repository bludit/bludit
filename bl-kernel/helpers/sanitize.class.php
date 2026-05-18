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

	// Validate a file path. With two arguments performs a path-traversal check:
	// the resolved $path.$file must live inside the resolved $path. With a single
	// argument no base is supplied, so only the existence of the file is checked
	// (callers needing traversal protection must pass the base directory as $path
	// and the untrusted segment as $file).
	public static function pathFile($path, $file=false)
	{
		if ($file!==false) {
			$fullPath = $path.$file;
		} else {
			$fullPath = $path;
		}

		// Fix for Windows on paths. eg: $path = c:\diego/page/subpage convert to c:\diego\page\subpages
		$fullPath = str_replace('/', DS, $fullPath);

		if (CHECK_SYMBOLIC_LINKS) {
			$real = realpath($fullPath);
		} else {
			$real = file_exists($fullPath) ? self::normalizePath($fullPath) : false;
		}

		// If $real is FALSE the file does not exist.
		if ($real===false) {
			return false;
		}

		// Without a base directory we cannot validate traversal; existence is all
		// we can answer.
		if ($file===false) {
			return true;
		}

		// Resolve the base directory to validate against path traversal.
		$basePath = realpath($path);
		if ($basePath===false) {
			return false;
		}

		// If the resolved path does not start with the base directory then this is Path Traversal.
		if ($real !== $basePath && strpos($real, $basePath . DS) !== 0) {
			return false;
		}

		return true;
	}

	// Resolves dot-segments in a path without following symlinks.
	private static function normalizePath($path)
	{
		$path = str_replace('/', DS, $path);
		$isAbsolute = (strlen($path) > 0 && $path[0] === DS);
		$parts = explode(DS, $path);
		$normalized = [];
		foreach ($parts as $part) {
			if ($part === '..') {
				array_pop($normalized);
			} elseif ($part !== '' && $part !== '.') {
				$normalized[] = $part;
			}
		}
		$result = implode(DS, $normalized);
		return $isAbsolute ? DS . $result : $result;
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