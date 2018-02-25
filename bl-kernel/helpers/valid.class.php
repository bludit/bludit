<?php defined('BLUDIT') or die('Bludit CMS.');

class Valid {

	public static function ip($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP);
	}

	// Returns the email filtered or FALSE if the filter fails.
	public static function email($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	public static function int($int)
	{
		if($int === 0) {
			return true;
		}
		elseif(filter_var($int, FILTER_VALIDATE_INT) === false ) {
			return false;
		}

		return true;
	}

	// Thanks, http://php.net/manual/en/function.checkdate.php#113205
	public static function date($date, $format='Y-m-d H:i:s')
	{
		$tmp = DateTime::createFromFormat($format, $date);

		return $tmp && $tmp->format($format)==$date;
	}

}
