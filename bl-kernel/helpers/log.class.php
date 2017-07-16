<?php defined('BLUDIT') or die('Bludit CMS.');

class Log {

	public static function set($text, $type=0)
	{
		if (is_array($text) ) {
			error_log('------------------------', $type);
			error_log('Array', $type);
			error_log('------------------------', $type);
			foreach ($text as $key=>$value) {
				error_log($key.'=>'.$value, $type);
			}
			error_log('------------------------', $type);
		}
		error_log('('.BLUDIT_VERSION.') ('.$_SERVER['REQUEST_URI'].') '.$text, $type);
	}

}
