<?php defined('BLUDIT') or die('Bludit CMS.');

class Crypt {

	// return string
	public static function encrypt($string, $key)
	{
		if(function_exists('get_loaded_extensions'))
		{
			if( in_array('mcrypt', get_loaded_extensions()) )
			{
				$string = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5($key) ) );
				return $string;
			}
		}

		return('---');
	}

	// return string
	public static function decrypt($string, $key)
	{
		if(function_exists('get_loaded_extensions'))
		{
			if( in_array('mcrypt', get_loaded_extensions()) )
			{
				$string = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5($key) ), "\0" );
				return $string;
			}
		}

		return('---');
	}

	public static function getHash($string, $salt = '$#!')
	{
		$sha1 = sha1($string.$salt);

		return($sha1);
	}
}
