<?php defined('BLUDIT') or die('Bludit CMS.');

class Cookie {

	public static function get($name)
	{
		if(isset($_COOKIE[$name]))
		{
			return($_COOKIE[$name]);
		}

		return(false);
	}

	public static function add($name, $value, $expire = 525600)
	{
		setcookie($name, $value, time() + ($expire * 60));
	}

	public static function isSet($name)
	{
		return(isset($_COOKIE[$name]));
	}

}
