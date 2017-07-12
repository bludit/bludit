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

	public static function css($files)
	{
		if( !is_array($files) ) {
			$files = array($files);
		}

		$links = '';
		foreach($files as $file) {
			$links .= '<link rel="stylesheet" type="text/css" href="'.DOMAIN_THEME.$file.'">'.PHP_EOL;
		}

		return $links;
	}

	public static function javascript($files)
	{
		if( !is_array($files) ) {
			$files = array($files);
		}

		$scripts = '';
		foreach($files as $file) {
			$scripts .= '<script src="'.DOMAIN_THEME.$file.'"></script>'.PHP_EOL;
		}

		return $scripts;
	}

	public static function js($files)
	{
		return self::javascript($files);
	}

	public static function plugins($type)
	{
		global $plugins;
		foreach($plugins[$type] as $plugin) {
			echo call_user_func(array($plugin, $type));
		}
	}

	public static function favicon($file='favicon.png', $typeIcon='image/png')
	{
		return '<link rel="shortcut icon" href="'.DOMAIN_THEME.$file.'" type="'.$typeIcon.'">'.PHP_EOL;
	}

	public static function fontAwesome()
	{
		return '<link rel="stylesheet" href="'.DOMAIN_CORE_CSS.'font-awesome/font-awesome.min.css'.'">'.PHP_EOL;
	}

	public static function jquery($cdn=false)
	{
		if($cdn) {
			return '<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>';
		}
		return '<script src="'.DOMAIN_CORE_JS.'jquery.min.js'.'"></script>'.PHP_EOL;
	}

// ---- OLD

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



}

?>