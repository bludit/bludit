<?php defined('BLUDIT') or die('Bludit CMS.');

class Log {

	public static function set($text, $type=0)
	{
		error_log('('.BLUDIT_VERSION.')'.$text, $type);
	}

}
