<?php

class Theme {

	// NEW

	public static function favicon($file='favicon.png', $path=HTML_PATH_THEME_IMG, $echo=true)
	{
		$tmp = '<link rel="shortcut icon" href="'.$path.$file.'" type="image/x-icon">'.PHP_EOL;

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

	public static function css($files, $path=HTML_PATH_THEME_CSS, $echo=true)
	{
		if(!is_array($files)) {
			$files = array($files);
		}

		$tmp = '';
		foreach($files as $file) {
			$tmp .= '<link rel="stylesheet" type="text/css" href="'.$path.$file.'">'.PHP_EOL;
		}

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

	public static function javascript($files, $path=HTML_PATH_THEME_JS, $echo=true)
	{
		if(!is_array($files)) {
			$files = array($files);
		}

		$tmp = '';
		foreach($files as $file) {
			$tmp .= '<script src="'.$path.$file.'"></script>'.PHP_EOL;
		}

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

	public static function title($title, $echo=true)
	{
		$tmp = '<title>'.$title.'</title>'.PHP_EOL;

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

	public static function description($description, $echo=true)
	{
		$tmp = '<meta name="description" content="'.$description.'">'.PHP_EOL;

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

	public static function keywords($keywords, $echo=true)
	{
		if(is_array($keywords)) {
			$keywords = implode(',', $keywords);
		}

		$tmp = '<meta name="keywords" content="'.$keywords.'">'.PHP_EOL;

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

	public static function viewport($content='width=device-width, initial-scale=1.0', $echo=true)
	{
		$tmp = '<meta name="viewport" content="'.$content.'">'.PHP_EOL;

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

	public static function charset($charset, $echo=true)
	{
		$tmp = '<meta charset="'.$charset.'">'.PHP_EOL;

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

	public static function plugins($type)
	{
		global $plugins;

		foreach($plugins[$type] as $plugin)
		{
			echo call_user_func(array($plugin, $type));
		}
	}

	public static function jquery($echo=true)
	{
		$tmp = '<script src="'.JQUERY.'"></script>'.PHP_EOL;

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

}

?>