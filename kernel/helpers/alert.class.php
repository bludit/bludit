<?php defined('BLUDIT') or die('Bludit CMS.');

class Alert {

	// Status, 0 = OK, 1 = Fail
	public static function set($value, $status=ALERT_STATUS_OK, $key='alert')
	{
		Session::set('defined', true);
		Session::set('alertStatus', $status);
		Session::set($key, $value);
	}

	public static function get($key='alert')
	{
		Session::set('defined', false);
		return Session::get($key);
	}

	public static function status()
	{
		return Session::get('alertStatus');
	}

	public static function p($key='alert')
	{
		echo self::get($key);
	}

	public static function defined()
	{
		return Session::get('defined');
	}

}
