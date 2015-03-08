<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Validation {

	public static function ip($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP);
	}

	public static function mail($mail)
	{
		return filter_var($mail, FILTER_VALIDATE_EMAIL);
	}

	public static function int($int)
	{
		if($int === 0)
			return( true );
		elseif (filter_var($int, FILTER_VALIDATE_INT) === false )
			return( false );
		else
			return( true );
	}

	// Remove all characters except digits
	public static function sanitize_float($value)
	{
		return( filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND) );
	}

	// Valid an integer positive
	public static function sanitize_int($value)
	{
		$value = (int)$value;

		if($value>=0)
			return $value;
		else
			return 0;
	}

	public static function sanitize_email($value)
	{
		return( filter_var($value, FILTER_SANITIZE_EMAIL) );
	}

	public static function sanitize_url($value)
	{
		return( filter_var($value, FILTER_SANITIZE_URL) );
	}

	// Convert all applicable characters to HTML entities incluye acentos
	public static function sanitize_html($text)
	{
		return(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
	}

}

?>
