<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Session {

	public static function init()
	{
		$_SESSION['nibbleblog'] = array(
			'error'=>false,
			'alert'=>'',
			'last_comment_at'=>0,
			'last_session_at'=>0,
			'fail_session'=>0
		);
	}

	public static function get($name)
	{
		if(isset($_SESSION['nibbleblog'][$name]))
			return $_SESSION['nibbleblog'][$name];
		else
			return false;
	}

	public static function set($key, $value)
	{
		$_SESSION['nibbleblog'][$key] = $value;
	}

	public static function get_error()
	{
		if(isset($_SESSION['nibbleblog']['error']))
		{
			return($_SESSION['nibbleblog']['error']);
		}

		return false;
	}

	public static function get_last_comment_at()
	{
		if(isset($_SESSION['nibbleblog']['last_comment_at']))
		{
			return($_SESSION['nibbleblog']['last_comment_at']);
		}

		return false;
	}

	public static function get_last_session_at()
	{
		if(isset($_SESSION['nibbleblog']['last_session_at']))
		{
			return($_SESSION['nibbleblog']['last_session_at']);
		}

		return false;
	}

	public static function get_fail_session()
	{
		if(isset($_SESSION['nibbleblog']['fail_session']))
		{
			return($_SESSION['nibbleblog']['fail_session']);
		}

		return false;
	}

	public static function get_comment($field)
	{
		if(isset($_SESSION['nibbleblog']['comment'][$field]))
			return $_SESSION['nibbleblog']['comment'][$field];

		return false;
	}

	public static function set_comment($field, $data)
	{
		$_SESSION['nibbleblog']['comment'][$field] = $data;
	}

	public static function get_alert()
	{
		self::set_error(false);
		return($_SESSION['nibbleblog']['alert']);
	}

	public static function set_error($boolean = true)
	{
		$_SESSION['nibbleblog']['error'] = $boolean;
	}

	public static function set_last_comment_at($time)
	{
		$_SESSION['nibbleblog']['last_comment_at'] = $time;
	}

	public static function set_last_session_at($time)
	{
		$_SESSION['nibbleblog']['last_session_at'] = $time;
	}

	public static function set_fail_session($amount)
	{
		$_SESSION['nibbleblog']['fail_session'] = $amount;
	}

	public static function set_alert($text = '')
	{
		self::set_error(true);
		$_SESSION['nibbleblog']['alert'] = $text;
	}

}

?>
