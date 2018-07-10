<?php

class Theme {

	public static function title()
	{
		global $Site;
		return $Site->title();
	}

	public static function description()
	{
		global $Site;
		return $Site->description();
	}

	public static function slogan()
	{
		global $Site;
		return $Site->slogan();
	}

	public static function footer()
	{
		global $Site;
		return $Site->footer();
	}

	public static function rssUrl()
	{
		if (pluginEnabled('pluginRSS')) {
			return DOMAIN_BASE.'rss.xml';
		}
		return false;
	}

	public static function sitemapUrl()
	{
		if (pluginEnabled('pluginSitemap')) {
			return DOMAIN_BASE.'sitemap.xml';
		}
		return false;
	}

	public static function siteUrl()
	{
		global $Site;
		return $Site->url();
	}

	public static function adminUrl()
	{
		return DOMAIN_ADMIN;
	}

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

		if (Text::isNotEmpty($Site->slogan())) {
			$title = $Site->slogan().' | '.$Site->title();
		}

		if ($WHERE_AM_I=='page') {
			$title = $page->title().' | '.$Site->title();
		}
		elseif ($WHERE_AM_I=='tag') {
			$tagKey = $Url->slug();
			$tagName = $dbTags->getName($tagKey);
			$title = $tagName.' | '.$Site->title();
		}
		elseif ($WHERE_AM_I=='category') {
			$categoryKey = $Url->slug();
			$categoryName = $dbCategories->getName($categoryKey);
			$title = $categoryName.' | '.$Site->title();
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

	public static function css($files, $base=DOMAIN_THEME)
	{
		if( !is_array($files) ) {
			$files = array($files);
		}

		$links = '';
		foreach($files as $file) {
			$links .= '<link rel="stylesheet" type="text/css" href="'.$base.$file.'?version='.BLUDIT_VERSION.'">'.PHP_EOL;
		}

		return $links;
	}

	public static function javascript($files, $base=DOMAIN_THEME)
	{
		if( !is_array($files) ) {
			$files = array($files);
		}

		$scripts = '';
		foreach($files as $file) {
			$scripts .= '<script charset="utf-8" src="'.$base.$file.'?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
		}

		return $scripts;
	}

	public static function js($files, $base=DOMAIN_THEME)
	{
		return self::javascript($files, $base);
	}

	public static function plugins($type)
	{
		global $plugins;
		foreach ($plugins[$type] as $plugin) {
			echo call_user_func(array($plugin, $type));
		}
	}

	public static function favicon($file='favicon.png', $typeIcon='image/png')
	{
		return '<link rel="shortcut icon" href="'.DOMAIN_THEME.$file.'" type="'.$typeIcon.'">'.PHP_EOL;
	}

	public static function keywords($keywords)
	{
		if (is_array($keywords)) {
			$keywords = implode(',', $keywords);
		}
		return '<meta name="keywords" content="'.$keywords.'">'.PHP_EOL;
	}

	public static function jquery()
	{
		return '<script charset="utf-8" src="'.DOMAIN_CORE_JS.'jquery.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
	}

	public static function bootstrapJS()
	{
		return '<script charset="utf-8" src="'.DOMAIN_CORE_JS.'bootstrap-bundle.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
	}

	public static function bootstrapCSS()
	{
		return '<link rel="stylesheet" type="text/css" href="'.DOMAIN_CORE_CSS.'bootstrap.min.css?version='.BLUDIT_VERSION.'">'.PHP_EOL;
	}
}

?>
