<?php defined('BLUDIT') or die('Bludit CMS.');

class Text {

	public static function addSlashes($string, $begin=true, $end=true)
	{
		if($begin) {
			$string = '/' . ltrim($string, '/');
		}

		if($end) {
			$string = rtrim($string, '/') . '/';
		}

		if($string=='//') {
			return '/';
		}

		return $string;
	}

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

/*
	public static function cleanUrl($string, $separator='-')
	{
		// Delete characters
		$string = str_replace(array("“", "”", "!", "*", "&#039;", "&quot;", "(", ")", ";", ":", "@", "&amp", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", "|"),'',$string);
		$string = preg_replace('![^\\pL\d]+!u', $separator, $string);

		// Remove spaces
		$string = str_replace(' ',$separator, $string);

		//remove any additional characters that might appear after translit
		//$string = preg_replace('![^-\w]+!', '', $string);

		// Replace multiple dashes
		$string = preg_replace('/-{2,}/', $separator, $string);

		// Make a string lowercase
		$string = self::lowercase($string);

		return $string;
	}
*/

	public static function cleanUrl($string, $separator='-')
	{
		if(function_exists('iconv')) {
			$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		}

		$string = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $string);
		$string = trim($string, '-');
		$string = self::lowercase($string);
		$string = preg_replace("/[\/_|+ -]+/", $separator, $string);

		return $string;
	}

	// Replace all occurrences of the search string with the replacement string.
	public static function replace($search, $replace, $string)
	{
		return str_replace($search,$replace,$string);
	}

	// String to lowercase
	public static function lowercase($string, $encoding='UTF-8')
	{
		if(MB_STRING) {
			return mb_strtolower($string, $encoding);
		}

		return strtolower($string);
	}

	// Make a string's first character uppercase
	public static function firstCharUp($string, $encoding='UTF-8')
	{
		// Thanks http://stackoverflow.com/questions/2517947/ucfirst-function-for-multibyte-character-encodings
		if(MB_STRING)
		{
		    $strlen 	= mb_strlen($string, $encoding);
		    $firstChar 	= mb_substr($string, 0, 1, $encoding);
		    $then 		= mb_substr($string, 1, $strlen - 1, $encoding);

		    return mb_strtoupper($firstChar, $encoding).$then;
		}

		return ucfirst($string);
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

	public static function isNotEmpty($string)
	{
		return !self::isEmpty($string);
	}

	public static function imgRel2Abs($string, $base)
	{
		return preg_replace('/(?!code).(src)="([^:"]*)(?:")/', "$1=\"$base$2\"", $string);
	}

	public static function pre2htmlentities($string)
	{
		return preg_replace_callback('/<pre.*?><code(.*?)>(.*?)<\/code><\/pre>/imsu',
			create_function('$input', 'return "<pre><code $input[1]>".htmlentities($input[2])."</code></pre>";'),
			$string);
	}

}
