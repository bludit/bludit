<?php

class Site
{
	private static $content;

	public static function init()
	{
		self::$content = '';
	}

	public static function content()
	{
		return self::$content;
	}

}

?>
