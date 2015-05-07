<?php defined('BLUDIT') or die('Bludit CMS.');

class helperText {

	// New

	public static function endsWith($string, $endsString)
	{
		$endsPosition = (-1)*self::length($endsString);

		if(MB_STRING) {
			return( mb_substr($string, $endsPosition)===$endsString );
		}

		return( substr($string, $endsPosition)===$endsString );
	}


	public static function endsWithNumeric($string)
	{
		$endsPosition = (-1)*self::length($string);

		if(MB_STRING) {
			return( is_numeric(mb_substr($string, -1, 1)) );
		}

		return( is_numeric(substr($string, -1, 1)) );
	}

	public static function randomText($length)
	{
		 $characteres = "1234567890abcdefghijklmnopqrstuvwxyz!@#%^&*";
		 $text = '';
		 for($i=0; $i<$length; $i++) {
			$text .= $characteres{rand(0,41)};
		 }
		 return $text;
	}

	public static function cleanUrl($string, $separator = '-')
	{
	    $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
	    $special_cases = array( '&' => 'and');
	    $string = self::lowercase( trim( $string ), 'UTF-8' );
	    $string = str_replace( array_keys($special_cases), array_values( $special_cases), $string );
	    $string = preg_replace( $accents_regex, '$1', htmlentities( $string, ENT_QUOTES, 'UTF-8' ) );
	    $string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
	    $string = preg_replace("/[$separator]+/u", "$separator", $string);
	    return $string;
	}

	// Replace all occurrences of the search string with the replacement string.
	public static function replace($search, $replace, $string)
	{
		return str_replace($search,$replace,$string);
	}

	// String to lowercase
	public static function lowercase($string)
	{
		if(MB_STRING)
			return mb_strtolower($string, 'UTF-8');
		return strtolower($string);
	}

	// Find position of first occurrence of substring in a string.
	public static function strpos($string, $substring)
	{
		if(MB_STRING)
			return mb_strpos($string, $substring, 0, 'UTF-8');
		return strpos($string, $substring);
	}

	// Returns the portion of string specified by the start and length parameters.
	public static function cut($string, $start, $length)
	{
		if(MB_STRING) {
			$cut = mb_substr($string, $start, $length, 'UTF-8');
		}
		else {
			$cut = substr($string, $start, $length);
		}

		if(empty($cut)) {
			return '';
		}

		return $cut;
	}

	// Return string length
	public static function length($string)
	{
		if(MB_STRING)
			return mb_strlen($string, 'UTF-8');
		return strlen($string);
	}

	public static function isEmpty($string)
	{
		$string = trim($string);

		if(empty($string))
			return true;

		return false;
	}

	// Old
	public static function unserialize($string)
	{
		parse_str($string, $data);

		// Clean magic quotes if this enabled
		if(get_magic_quotes_gpc())
		{
			$data = self::clean_magic_quotes($data);
		}

		return($data);
	}

	public static function ajax_header($tmp)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		$xml .= '<ajax>';
		$xml .= $tmp;
		$xml .= '</ajax>';
		return( $xml );
	}

	// Clean magic quotes
	public static function clean_magic_quotes($args)
	{
		$tmp_array = array();
		foreach($args as $key => $arg)
		{
			$tmp_array[$key] = stripslashes($arg);
		}

		return($tmp_array);
	}

	public static function cut_text($text, $maxlength)
	{
		return( substr($text,0,strrpos(substr($text,0,$maxlength)," ")) );
	}

	public static function cut_words($text, $count)
	{
		$explode = explode(" ", $text);

		if(count($explode) > $count)
		{
			array_splice($explode, $count);
			$text = implode(' ', $explode);
		}

		return($text);
	}

	// Strip spaces


	// Strip spaces
	public static function strip_spaces($string)
	{
		return( str_replace(' ','',$string) );
	}

	// Strip quotes ' and "
	public static function strip_quotes($text)
	{
		$text = str_replace('\'', '', $text);
		$text = str_replace('"', '', $text);
		return( $text );
	}

	function clean_non_alphanumeric($string)
	{
		$string = preg_replace("/[^A-Za-z0-9 ]/", '', $string);

		return $string;
	}

	// RETURN
	// TRUE - si contiene el substring
	// FALSE - caso contrario
	public static function is_substring($string, $substring)
	{
		return( strpos($string, $substring) !== false );
	}

	// RETURN
	// TRUE - is not empty
	// FALSE - is empty
	public static function not_empty($string)
	{
		return( !self::is_empty($string) );
	}

	public static function is_empty($string)
	{
		$string = self::strip_spaces($string);
		return( empty($string) );
	}

	// Compara 2 cadenas
	// Retorna TRUE si son iguales, FALSE caso contrario
	public static function compare($value1, $value2)
	{
		return( strcmp($value1, $value2) == 0 );
	}

	// Clean text for URL
	public static function clean_url($text, $spaces='-', $translit=false)
	{
		// Delete characters
		$text = str_replace(array("“", "”", "!", "*", "&#039;", "&quot;", "(", ")", ";", ":", "@", "&amp", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", "|"),'',$text);
		$text = preg_replace('![^\\pL\d]+!u', '-', $text);

		// Translit
		if($translit!=false)
		{
			$text = str_replace(array_keys($translit),array_values($translit),$text);
		}
    		if (function_exists('iconv'))
		{
			$ret = iconv('utf-8', 'us-ascii//TRANSLIT//IGNORE', $text);
			if ($ret!==false){ //iconv might return false on error
				$text = $ret;
			}
		}

		// Replace spaces by $spaces
		$text = str_replace(' ',$spaces,$text);

		//remove any additional characters that might appear after translit
		$text = preg_replace('![^-\w]+!', '', $text);

		// Replace multiple dashes
		$text = preg_replace('/-{2,}/', '-', $text);

		// Make a string lowercase
		$text = self::str2lower($text);

		return $text;
	}





	public static function replace_assoc(array $replace, $text)
	{
		return str_replace(array_keys($replace), array_values($replace), $text);
	}

}
