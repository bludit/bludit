<?php

class Theme {

	// Return the metatag <title> with a predefine structure
	public static function headTitle()
	{
		global $Url;
		global $Site;
		global $dbTags;
		global $dbCategories;
		global $WHERE_AM_I;
		global $page;

		$title = $Site->title();

		if( $WHERE_AM_I=='page' ) {
			$title = $page->title().' - '.$Site->title();
		}
		elseif( $WHERE_AM_I=='tag' ) {
			$tagKey = $Url->slug();
			$tagName = $dbTags->getName($tagKey);
			$title = $tagName.' - '.$Site->title();
		}
		elseif( $WHERE_AM_I=='category' ) {
			$categoryKey = $Url->slug();
			$categoryName = $dbCategories->getName($categoryKey);
			$title = $categoryName.' - '.$Site->title();
		}

		return '<title>'.$title.'</title>'.PHP_EOL;
	}

	// Return the metatag <decription> with a predefine structure
	public static function headDescription()
	{
		global $Site;
		global $WHERE_AM_I;
		global $page;

		$description = $Site->description();

		if( $WHERE_AM_I=='page' ) {
			$description = $page->description();
		}

		return '<meta name="description" content="'.$description.'">'.PHP_EOL;
	}

	public static function charset($charset)
	{
		return '<meta charset="'.$charset.'">'.PHP_EOL;
	}

	public static function viewport($content)
	{
		return '<meta name="viewport" content="'.$content.'">'.PHP_EOL;
	}

	public static function css($files, $path=HTML_PATH_THEME)
	{
		if( !is_array($files) ) {
			$files = array($files);
		}

		$links = '';
		foreach($files as $file) {
			$links .= '<link rel="stylesheet" type="text/css" href="'.$path.$file.'">'.PHP_EOL;
		}

		return $links;
	}

	public static function javascript($files, $path=HTML_PATH_THEME)
	{
		if( !is_array($files) ) {
			$files = array($files);
		}

		$scripts = '';
		foreach($files as $file) {
			$scripts .= '<script src="'.$path.$file.'"></script>'.PHP_EOL;
		}

		return $scripts;
	}

	public static function js($files, $path=HTML_PATH_THEME)
	{
		self::javascript($files, $path);
	}

	public static function plugins($type)
	{
		global $plugins;
		foreach($plugins[$type] as $plugin) {
			echo call_user_func(array($plugin, $type));
		}
	}

// ---- OLD

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