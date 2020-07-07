<?php defined('BLUDIT') or die('Bludit CMS.');

class Text {

	private static $unicodeChars = array(
		// Latin
		'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'AE', 'Ç'=>'C',
		'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
		'Ð'=>'D', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ő'=>'O',
		'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ű'=>'U', 'Ý'=>'Y', 'Þ'=>'TH',
		'ß'=>'ss',
		'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'ae', 'ç'=>'c',
		'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
		'ð'=>'d', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ő'=>'o',
		'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ű'=>'u', 'ý'=>'y', 'þ'=>'th',
		'ÿ'=>'y',
		// Latin symbols
		'©'=>'(c)',
		// Greek
		'Α'=>'A', 'Β'=>'B', 'Γ'=>'G', 'Δ'=>'D', 'Ε'=>'E', 'Ζ'=>'Z', 'Η'=>'H', 'Θ'=>'8',
		'Ι'=>'I', 'Κ'=>'K', 'Λ'=>'L', 'Μ'=>'M', 'Ν'=>'N', 'Ξ'=>'3', 'Ο'=>'O', 'Π'=>'P',
		'Ρ'=>'R', 'Σ'=>'S', 'Τ'=>'T', 'Υ'=>'Y', 'Φ'=>'F', 'Χ'=>'X', 'Ψ'=>'PS', 'Ω'=>'W',
		'Ά'=>'A', 'Έ'=>'E', 'Ί'=>'I', 'Ό'=>'O', 'Ύ'=>'Y', 'Ή'=>'H', 'Ώ'=>'W', 'Ϊ'=>'I',
		'Ϋ'=>'Y',
		'α'=>'a', 'β'=>'b', 'γ'=>'g', 'δ'=>'d', 'ε'=>'e', 'ζ'=>'z', 'η'=>'h', 'θ'=>'8',
		'ι'=>'i', 'κ'=>'k', 'λ'=>'l', 'μ'=>'m', 'ν'=>'n', 'ξ'=>'3', 'ο'=>'o', 'π'=>'p',
		'ρ'=>'r', 'σ'=>'s', 'τ'=>'t', 'υ'=>'y', 'φ'=>'f', 'χ'=>'x', 'ψ'=>'ps', 'ω'=>'w',
		'ά'=>'a', 'έ'=>'e', 'ί'=>'i', 'ό'=>'o', 'ύ'=>'y', 'ή'=>'h', 'ώ'=>'w', 'ς'=>'s',
		'ϊ'=>'i', 'ΰ'=>'y', 'ϋ'=>'y', 'ΐ'=>'i',
		// Turkish
		'Ş'=>'S', 'İ'=>'I', 'Ç'=>'C', 'Ü'=>'U', 'Ö'=>'O', 'Ğ'=>'G',
		'ş'=>'s', 'ı'=>'i', 'ç'=>'c', 'ü'=>'u', 'ö'=>'o', 'ğ'=>'g',
		// Russian
		'А'=>'A', 'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ё'=>'Yo', 'Ж'=>'Zh',
		'З'=>'Z', 'И'=>'I', 'Й'=>'J', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O',
		'П'=>'P', 'Р'=>'R', 'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Х'=>'H', 'Ц'=>'C',
		'Ч'=>'Ch', 'Ш'=>'Sh', 'Щ'=>'Sh', 'Ъ'=>'', 'Ы'=>'Y', 'Ь'=>'', 'Э'=>'E', 'Ю'=>'Yu',
		'Я'=>'Ya',
		'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ё'=>'yo', 'ж'=>'zh',
		'з'=>'z', 'и'=>'i', 'й'=>'j', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o',
		'п'=>'p', 'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'х'=>'h', 'ц'=>'c',
		'ч'=>'ch', 'ш'=>'sh', 'щ'=>'sh', 'ъ'=>'', 'ы'=>'y', 'ь'=>'', 'э'=>'e', 'ю'=>'yu',
		'я'=>'ya',
		// Bulgarian
		'А'=>'A', 'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ж'=>'Zh', 'З'=>'Z',
		'И'=>'I', 'Й'=>'J', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P',
		'Р'=>'R', 'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Х'=>'H', 'Ц'=>'C', 'Ч'=>'Ch',
		'Ш'=>'Sh', 'Щ'=>'Sh', 'Ъ'=>'',	'Ь'=>'J','Ю'=>'Yu','Я'=>'Ya',
		'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ж'=>'zh','з'=>'z',
		'и'=>'i', 'й'=>'j', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o','п'=>'p',
		'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'х'=>'h', 'ц'=>'c', 'ч'=>'ch',
		'ш'=>'sh', 'щ'=>'sh', 'ъ'=>'', 'ь'=>'j', 'ю'=>'yu', 'я'=>'ya',
		// Ukrainian
		'Є'=>'Ye', 'І'=>'I', 'Ї'=>'Yi', 'Ґ'=>'G',
		'є'=>'ye', 'і'=>'i', 'ї'=>'yi', 'ґ'=>'g',
		// Czech
		'Č'=>'C', 'Ď'=>'D', 'Ě'=>'E', 'Ň'=>'N', 'Ř'=>'R', 'Š'=>'S', 'Ť'=>'T', 'Ů'=>'U',
		'Ž'=>'Z',
		'č'=>'c', 'ď'=>'d', 'ě'=>'e', 'ň'=>'n', 'ř'=>'r', 'š'=>'s', 'ť'=>'t', 'ů'=>'u',
		'ž'=>'z',
		// Polish
		'Ą'=>'A', 'Ć'=>'C', 'Ę'=>'e', 'Ł'=>'L', 'Ń'=>'N', 'Ó'=>'o', 'Ś'=>'S', 'Ź'=>'Z',
		'Ż'=>'Z',
		'ą'=>'a', 'ć'=>'c', 'ę'=>'e', 'ł'=>'l', 'ń'=>'n', 'ó'=>'o', 'ś'=>'s', 'ź'=>'z',
		'ż'=>'z',
		// Latvian
		'Ā'=>'A', 'Č'=>'C', 'Ē'=>'E', 'Ģ'=>'G', 'Ī'=>'i', 'Ķ'=>'k', 'Ļ'=>'L', 'Ņ'=>'N',
		'Š'=>'S', 'Ū'=>'u', 'Ž'=>'Z',
		'ā'=>'a', 'č'=>'c', 'ē'=>'e', 'ģ'=>'g', 'ī'=>'i', 'ķ'=>'k', 'ļ'=>'l', 'ņ'=>'n',
		'š'=>'s', 'ū'=>'u', 'ž'=>'z'
	);

	public static function addSlashes($string, $begin=true, $end=true)
	{
		if ($begin) {
			$string = '/'.ltrim($string, '/');
		}

		if ($end) {
			$string = rtrim($string, '/').'/';
		}

		if ($string=='//') {
			return '/';
		}

		return $string;
	}

	// Escape quotes and backslash
	public static function escapeQuotes($string)
	{
		return addslashes($string);
	}

	public static function startsWith($string, $startString)
	{
		$length = self::length($startString);

		return( mb_substr($string, 0, $length)===$startString );
	}

	public static function endsWith($string, $endsString)
	{
		//$length = (-1)*self::length($endsString);
		return (mb_substr($string, -1)===$endsString);
	}

	public static function endsWithNumeric($string)
	{
		return( is_numeric(mb_substr($string, -1, 1)) );
	}

	public static function randomText($length)
	{
		 $characteres = "1234567890abcdefghijklmnopqrstuvwxyz!@#%^&*";
		 $text = '';
		 for($i=0; $i<$length; $i++) {
			$text .= $characteres[rand(0,41)];
		 }
		 return $text;
	}

	public static function replaceAssoc(array $replace, $text)
	{
		return str_replace(array_keys($replace), array_values($replace), $text);
	}

	public static function removeSpecialCharacters($string, $replace='')
	{
		return preg_replace("/[\/_|+:!@#$%^&*()'\"<>\\\`}{;=,?\[\]~. -]+/", $replace, $string);
	}

	public static function removeQuotes($string, $replace='')
	{
		$remove = array('\''=>$replace, '"'=>$replace);
		return self::replaceAssoc($remove, $string);
	}

	public static function removeLineBreaks($string)
	{
		$remove = array("\r"=>'', "\n"=>'');
		return self::replaceAssoc($remove, $string);
	}

	public static function removeSpaces($string, $replace='')
	{
		$remove = array(' '=>$replace);
		return self::replaceAssoc($remove, $string);
	}

	// Convert unicode characters to utf-8 characters
	// Characters that cannot be converted will be removed from the string
	// This function can return an empty string
	public static function cleanUrl($string, $separator='-')
	{
		global $L;

		if (EXTREME_FRIENDLY_URL) {
			$string = self::lowercase($string);
			$string = trim($string, $separator);
			$string = self::removeSpecialCharacters($string, $separator);
			$string = self::removeLineBreaks($string);
			$string = trim($string, $separator);
			return $string;
		}

		// Transliterate characters to ASCII
		$unicodeCharsFromDictionary = $L->getunicodeChars();
		$string = str_replace(array_keys($unicodeCharsFromDictionary), $unicodeCharsFromDictionary, $string);
		$string = str_replace(array_keys(self::$unicodeChars), self::$unicodeChars, $string);

		if (function_exists('iconv')) {
			if (@iconv(CHARSET, 'ASCII//TRANSLIT//IGNORE', $string)!==false) {
				$string = iconv(CHARSET, 'ASCII//TRANSLIT//IGNORE', $string);
			}
		}

		$string = preg_replace("/[^a-zA-Z0-9\/_|+. -]/", '', $string);
		$string = self::lowercase($string);
		$string = preg_replace("/[\/_|+ -]+/", $separator, $string);
		$string = trim($string, '-');

		return $string;
	}

	// Replace all occurrences of the search string with the replacement string.
	// replace("%body%", "black", "<body text='%body%'>");
	public static function replace($search, $replace, $string)
	{
		return str_replace($search,$replace,$string);
	}

	// String to lowercase
	public static function lowercase($string)
	{
		return mb_strtolower($string, CHARSET);
	}

	// Make a string's first character uppercase
	public static function firstCharUp($string)
	{
		// Thanks http://stackoverflow.com/questions/2517947/ucfirst-function-for-multibyte-character-encodings
		$strlen 	= mb_strlen($string, CHARSET);
		$firstChar 	= mb_substr($string, 0, 1, CHARSET);
		$then 		= mb_substr($string, 1, $strlen - 1, CHARSET);

		return mb_strtoupper($firstChar, CHARSET).$then;
	}

	// Find position of first occurrence of substring in a string otherwise returns FALSE.
	public static function stringPosition($string, $substring, $caseSensitive=true)
	{
		if ($caseSensitive) {
			return mb_strpos($string, $substring, 0, CHARSET);
		}

		return mb_stripos($string, $substring, 0, CHARSET);
	}

	public static function stringContains($string, $substring, $caseSensitive=true)
	{
		return (self::stringPosition($string, $substring, $caseSensitive) !== false);
	}

	// Returns the portion of string specified by the start and length parameters.
	public static function cut($string, $start, $length)
	{
		$cut = mb_substr($string, $start, $length, CHARSET);

		if(empty($cut)) {
			return '';
		}

		return $cut;
	}

	public static function removeHTMLTags($string)
	{
		return strip_tags($string);
	}

	// Return string length
	public static function length($string)
	{
		return mb_strlen($string, CHARSET);
	}

	public static function isEmpty($string)
	{
		$string = trim($string);

		if(empty($string)) {
			return true;
		}

		return false;
	}

	public static function isNotEmpty($string)
	{
		return !self::isEmpty($string);
	}

	public static function imgRel2Abs($string, $base)
	{
		$pattern = '/<img([^>]*)(src)=\"(?!https:)(?!http:)(?!\/\/)(.*?)\"(.*?)>/';
		$replace = "<img\${1} src=\"".$base."\${3}\" \${4}>";

		return preg_replace($pattern, $replace, $string);
	}

	public static function pre2htmlentities($string)
	{
		return preg_replace_callback('/<pre.*?><code(.*?)>(.*?)<\/code><\/pre>/imsu',
		        function ($input) {
                                return "<pre><code $input[1]>".htmlentities($input[2])."</code></pre>";
                        },
			$string);
	}

	// Truncates the string under the limit specified by the limit parameter
	public static function truncate($string, $limit, $end='...')
	{
		// Check if over $limit
		if (mb_strlen($string) > $limit) {
			$truncate = trim(mb_substr($string, 0, $limit, CHARSET));
			$truncate = $truncate.$end;
		} else {
			$truncate = $string;
		}

		if (empty($truncate)) {
			return '';
		}

		return $truncate;
	}

	public static function toBytes($value) {
		$value = trim($value);
		$s = [ 'g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10 ];
		return intval($value) * ($s[strtolower(substr($value,-1))] ?: 1);
	}

}
