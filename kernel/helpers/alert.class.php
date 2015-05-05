<?php defined('BLUDIT') or die('Bludit CMS.');

class Alert {

	// new
	public static function set($value, $key='alert')
	{
		Session::set('displayed', false);

		Session::set($key, $value);
	}

	public static function get($key='alert')
	{
		Session::set('displayed', true);

		return Session::get($key);
	}

	public static function displayed()
	{
		return Session::get('displayed');
	}

}
