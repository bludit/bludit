<?php

class Theme {

	public static function favicon($file='favicon.png', $path=HTML_PATH_THEME_IMG, $typeIcon=true, $echo=true)
	{
		$type = 'image/png';
		if($typeIcon) {
			$type = 'image/x-icon';
		}

		$tmp = '<link rel="shortcut icon" href="'.$path.$file.'" type="'.$type.'">'.PHP_EOL;

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

	public static function title($title=false, $echo=true)
	{
		global $Url;
		global $Post, $Page;
		global $Site;

		$tmp = $title;

		if(empty($title))
		{
			if( $Url->whereAmI()=='post' ) {
				$tmp = $Post->title().' - '.$Site->title();
			}
			elseif( $Url->whereAmI()=='page' ) {
				$tmp = $Page->title().' - '.$Site->title();
			}
			else {
				$tmp = $Site->title();
			}
		}

		$tmp = '<title>'.$tmp.'</title>'.PHP_EOL;

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

	public static function description($description=false, $echo=true)
	{
		global $Url;
		global $Post, $Page;
		global $Site;

		$tmp = $description;

		if(empty($description))
		{
			if( $Url->whereAmI()=='post' ) {
				$tmp = $Post->description();
			}
			elseif( $Url->whereAmI()=='page' ) {
				$tmp = $Page->description();
			}
			else {
				$tmp = $Site->description();
			}
		}

		$tmp = '<meta name="description" content="'.$tmp.'">'.PHP_EOL;

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
		$tmp = '<script src="'.HTML_PATH_ADMIN_THEME_JS.'jquery.min.js'.'"></script>'.PHP_EOL;

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}

	public static function fontAwesome($echo=true, $online=false)
	{
		$tmp = '<link rel="stylesheet" href="'.HTML_PATH_ADMIN_THEME_CSS.'font-awesome.min.css'.'">'.PHP_EOL;

		if($echo) {
			echo $tmp;
		}

		return $tmp;
	}


}

?>