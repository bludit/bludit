<?php defined('BLUDIT') or die('Bludit CMS.');

class Redirect {

	public static function url($url, $httpCode=301)
	{
		if (!headers_sent()) {
			header("Location:".$url, TRUE, $httpCode);
			exit(0);
		}

		exit('<meta http-equiv="refresh" content="0; url='.$url.'"/>');
	}

	public static function page($page)
	{
		self::url(HTML_PATH_ADMIN_ROOT.$page);
	}

	public static function home()
	{
		self::url(HTML_PATH_ROOT);
	}

	public static function admin()
	{
		self::url(HTML_PATH_ADMIN_ROOT);
	}
}