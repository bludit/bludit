<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class Page {

	public static function title()
	{
		global $page;

		return $page['title'];
	}

	public static function permalink($absolute=false)
	{
		global $page;

		return helperUrl::page($page,$absolute);
	}

	public static function published($format=false)
	{
		global $page;
		global $settings;

		$format = $format===false?$settings['timestamp_format']:$format;

		return Date::format($page['pub_date_unix'], $format);
	}

	public static function modified($format=false)
	{
		global $page;
		global $settings;

		$format = $format===false?$settings['timestamp_format']:$format;

		return Date::format($page['mod_date_unix'], $format);
	}

	public static function content()
	{
		global $page;

		return $page['content'];
	}

}

?>
