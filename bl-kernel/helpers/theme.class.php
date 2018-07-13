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

	public static function metaTags($tag)
	{
		if ($tag=='title') {
			return self::metaTagTitle();
		} elseif ($tag=='description') {
			return self::metaTagDescription();
		}
	}

	public static function metaTagTitle()
	{
		global $url;
		global $site;
		global $dbTags;
		global $dbCategories;
		global $WHERE_AM_I;
		global $page;

		if ($WHERE_AM_I=='page') {
			$format = $site->titleFormatPages();
			$format = Text::replace('{{page-title}}', $page->title(), $format);
			$format = Text::replace('{{page-description}}', $page->description(), $format);
		}
		elseif ($WHERE_AM_I=='tag') {
			$tagKey = $url->slug();
			$tagName = $dbTags->getName($tagKey);
			$format = $site->titleFormatTag();
			$format = Text::replace('{{tag-name}}', $tagName, $format);
		}
		elseif ($WHERE_AM_I=='category') {
			$categoryKey = $url->slug();
			$categoryName = $dbCategories->getName($categoryKey);
			$format = $site->titleFormatCategory();
			$format = Text::replace('{{category-name}}', $categoryName, $format);
		} else {
			$format = $site->titleFormatHomepage();
		}

		$format = Text::replace('{{site-title}}', $site->title(), $format);
		$format = Text::replace('{{site-slogan}}', $site->slogan(), $format);
		$format = Text::replace('{{site-description}}', $site->description(), $format);

		return '<title>'.$format.'</title>'.PHP_EOL;
	}

	public static function metaTagDescription()
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

	// DEPRECATED v3.0.0
	// Return the metatag <title> with a predefine structure
	public static function headTitle()
	{
		return self::metaTagTitle();
	}

	// DEPRECATED v3.0.0
	// Return the metatag <decription> with a predefine structure
	public static function headDescription()
	{
		return self::metaTagDescription();
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

	public static function jsBootstrap()
	{
		return '<script charset="utf-8" src="'.DOMAIN_CORE_JS.'bootstrap-bundle.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
	}

	public static function cssBootstrap()
	{
		return '<link rel="stylesheet" type="text/css" href="'.DOMAIN_CORE_CSS.'bootstrap.min.css?version='.BLUDIT_VERSION.'">'.PHP_EOL;
	}
}

?>
