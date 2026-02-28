<?php defined('BLUDIT') or die('Bludit CMS.');

class Cookie {

	public static function get($key)
	{
		if (isset($_COOKIE[$key])) {
			return $_COOKIE[$key];
		}
		return false;
	}

	public static function set($key, $value, $daysToExpire=30, $options=array())
	{
		$expire = time() + 60 * 60 * 24 * $daysToExpire;
		$secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

		$defaults = array(
			'expires'  => $expire,
			'path'     => '/',
			'domain'   => '',
			'secure'   => $secure,
			'httponly'  => true,
			'samesite' => 'Lax'
		);

		setcookie($key, $value, array_merge($defaults, $options));
	}

	public static function remove($key)
	{
		unset($_COOKIE[$key]);
		self::set($key, '', -1);
	}

	public static function isEmpty($key)
	{
		return empty($_COOKIE[$key]);
	}
}
