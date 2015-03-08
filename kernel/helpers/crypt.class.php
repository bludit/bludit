<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

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

	public static function get_hash($string, $salt = '$#!')
	{
		$sha1 = sha1($string.$salt);

		return($sha1);
	}
}

?>
