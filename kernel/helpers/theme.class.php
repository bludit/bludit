<?php

class Theme {

	// NEW

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


	// OLD

	public static function url($relative = true)
	{
		if($relative)
			return HTML_PATH_ROOT;
		else
			return BLOG_URL;
	}

	public static function jquery($path=JS_JQUERY)
	{
		$tmp = '<script src="'.$path.'"></script>'.PHP_EOL;

		return $tmp;
	}

	public static function favicon()
	{
		return '<link rel="shortcut icon" href="'.HTML_THEME_CSS.'img/favicon.ico" type="image/x-icon">'.PHP_EOL;
	}

	public static function name()
	{
		global $settings;

		return $settings['name'];
	}

	public static function slogan()
	{
		global $settings;

		return $settings['slogan'];
	}

	public static function footer()
	{
		global $settings;

		return $settings['footer'];
	}

	public static function language()
	{
		global $settings;

		$lang = explode("_",$settings['locale']);

		return $lang[0];
	}

	public static function locale()
	{
		global $settings;

		return $settings['locale'];
	}

	public static function meta_tags()
	{
		global $layout;
		global $seo;

		// The validator W3C doesn't support???
		//$meta = '<meta charset="UTF-8">'.PHP_EOL;

		$meta = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'.PHP_EOL;

		if(!empty($layout['title']))
			$meta .= '<title>'.$layout['title'].'</title>'.PHP_EOL;

		if(!empty($layout['description']))
			$meta .= '<meta name="description" content="'.$layout['description'].'">'.PHP_EOL;

		if(!empty($layout['generator']))
			$meta .= '<meta name="generator" content="'.$layout['generator'].'">'.PHP_EOL;

		if(!empty($layout['keywords']))
			$meta .= '<meta name="keywords" content="'.$layout['keywords'].'">'.PHP_EOL;

		if(!empty($layout['author']))
		{
			if(filter_var($layout['author'], FILTER_VALIDATE_URL))
				$meta .= '<link rel="author" href="'.$layout['author'].'">'.PHP_EOL;
			else
				$meta .= '<meta name="author" content="'.$layout['author'].'">'.PHP_EOL;
		}

		if(!empty($layout['canonical']))
			$meta .= '<link rel="canonical" href="'.$layout['canonical'].'">'.PHP_EOL;

		if(!empty($layout['robots']))
			$meta .= '<meta name="robots" content="'.$layout['robots'].'">'.PHP_EOL;

		if(!empty($seo['google_code']))
			$meta .= '<meta name="google-site-verification" content="'.$seo['google_code'].'">'.PHP_EOL;

		if(!empty($seo['bing_code']))
			$meta .= '<meta name="msvalidate.01" content="'.$seo['bing_code'].'">'.PHP_EOL;

		$meta .= '<link rel="alternate" type="application/atom+xml" title="ATOM Feed" href="'.$layout['feed'].'">'.PHP_EOL;

		return $meta;
	}

}

?>
