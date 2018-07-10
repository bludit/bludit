<?php defined('BLUDIT') or die('Bludit CMS.');

class Log {

	public static function set($text, $type=LOG_TYPE_INFO)
	{
		$messageType = 0;

		if (is_array($text) ) {
			error_log('------------------------', $messageType);
			error_log('Array', $messageType);
			error_log('------------------------', $messageType);
			foreach ($text as $key=>$value) {
				error_log($key.'=>'.$value, $messageType);
			}
			error_log('------------------------', $messageType);
		}
		error_log($type.' ['.BLUDIT_VERSION.'] ['.$_SERVER['REQUEST_URI'].'] '.$text, $messageType);
	}

}
