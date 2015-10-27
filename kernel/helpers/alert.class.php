<?php defined('BLUDIT') or die('Bludit CMS.');

class Alert {

	// new
	public static function set($value, $key='alert')
	{
		Session::set('defined', true);

		Session::set($key, $value);
	}

	public static function get($key='alert')
	{
		Session::set('defined', false);

		return Session::get($key);
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