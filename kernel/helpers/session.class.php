<?php

class Session {

	private static $started = false;

	public static function start()
	{
		if(self::$started)
			return true;
		
		self::$started = session_start();
	}

	public static function started()
	{
		return self::$started;
	}

	public static function destroy()
	{
		session_destroy();

		unset($_SESSION);
		
		self::$started = false;
	}

	public static function set($key, $value)
	{
		$key = 's_'.$key;
		
		$_SESSION[$key] = $value;
	}

	public static function get($key)
	{
		$key = 's_'.$key;

		if( isset($_SESSION[$key]) )
			return $_SESSION[$key];

		return false;
	}
}

?>
