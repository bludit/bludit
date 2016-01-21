<?php defined('BLUDIT') or die('Bludit CMS.');

class Date {

	// Return current Unix timestamp, GMT+0
	public static function unixTime()
	{
		return time();
	}

	// Return the local time/date according to locale settings.
	public static function current($format)
	{
		$Date = new DateTime();
		return $Date->format($format);
	}

	public static function currentOffset($format, $offset)
	{
		$Date = new DateTime();
		$Date->modify($offset);
		return $Date->format($format);
	}

	// Format a local time/date according to locale settings.
	public static function format($date, $currentFormat, $outputFormat)
	{
		// Returns a new DateTime instance or FALSE on failure.
		$Date = DateTime::createFromFormat($currentFormat, $date);

		if($Date!==false) {
			return $Date->format($outputFormat);
		}

		return false;
	}

	public static function timeago($time)
	{
		$time = time() - $time;

		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
		);

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		}
	}

	// DEBUG: Check this function, need to be more fast
	// Return array('Africa/Abidjan'=>'Africa/Abidjan (GMT+0)', ..., 'Pacific/Wallis'=>'Pacific/Wallis (GMT+12)');
	// PHP supported list. http://php.net/manual/en/timezones.php
	public static function timezoneList()
	{
		$tmp = array();

		$timezone_identifiers_list = timezone_identifiers_list();

		foreach($timezone_identifiers_list as $timezone_identifier)
		{
			$date_time_zone = new DateTimeZone($timezone_identifier);
			$date_time = new DateTime('now', $date_time_zone);

			$hours = floor($date_time_zone->getOffset($date_time) / 3600);
			$mins = floor(($date_time_zone->getOffset($date_time) - ($hours*3600)) / 60);

			$hours = 'GMT' . ($hours < 0 ? $hours : '+'.$hours);
			$mins = ($mins > 0 ? $mins : '0'.$mins);

			$text = str_replace("_"," ",$timezone_identifier);

			$tmp[$timezone_identifier] = $text.' ('.$hours.':'.$mins.')';
		}

		return $tmp;
	}
}
