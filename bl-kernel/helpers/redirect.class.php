<?php defined('BLUDIT') or die('Bludit CMS.');

class Redirect {

	public static function url($url)
	{
		if(!headers_sent()) {
			header("Location:".$url, TRUE, 302);
			exit;
		}

		exit('<meta http-equiv="refresh" content="0; url='.$url.'"/>');
	}

	public static function page($page)
	{
		$base = ltrim(ADMIN_URI_FILTER, '/');
		self::url(HTML_PATH_ROOT.$base.$page);
	}

	public static function home()
	{
		self::url(HTML_PATH_ROOT);
	}

}