<?php

class Alert {

	// new
	public static function set($value, $key='alert')
	{
		Session::set($key, $value);
	}

	public static function get($key='alert')
	{
		return Session::get($key);
	}


}

?>
