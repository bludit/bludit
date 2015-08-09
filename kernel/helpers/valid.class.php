<?php defined('BLUDIT') or die('Bludit CMS.');

class Valid {

	public static function ip($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP);
	}

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

}