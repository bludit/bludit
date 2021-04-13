<?php defined('BLUDIT') or die('Bludit CMS.');

class HTML {

	public static function css($files, $base=DOMAIN_THEME, $attributes='')
	{
		if (!is_array($files)) {
			$files = array($files);
		}

		$links = '';
		foreach ($files as $file) {
			$links .= '<link '.$attributes.' rel="stylesheet" type="text/css" href="'.$base.$file.'?version='.BLUDIT_VERSION.'">'.PHP_EOL;
		}
		return $links;
	}

	public static function javascript($files, $base=DOMAIN_THEME, $attributes='')
	{
		if( !is_array($files) ) {
			$files = array($files);
		}

		$scripts = '';
		foreach($files as $file) {
			$scripts .= '<script '.$attributes.' src="'.$base.$file.'?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
		}
		return $scripts;
	}

	public static function js($files, $base=DOMAIN_THEME, $attributes='')
	{
		return self::javascript($files, $base, $attributes);
	}

	public static function favicon($file='favicon.png', $typeIcon='image/png')
	{
		return '<link rel="icon" href="'.DOMAIN_THEME.$file.'" type="'.$typeIcon.'">'.PHP_EOL;
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
		// https://jquery.com/
		return '<script src="'.DOMAIN_CORE_VENDORS.'jquery/jquery.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
	}

	public static function bootbox($attributes='')
	{
		// https://bootbox.com/
		return '<script '.$attributes.' src="'.DOMAIN_CORE_VENDORS.'bootbox/bootbox.all.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
	}

	public static function jsBootstrap($attributes='')
	{
		// https://getbootstrap.com/
		return '<script '.$attributes.' src="'.DOMAIN_CORE_VENDORS.'bootstrap/bootstrap.bundle.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
	}

	public static function cssBootstrap()
	{
		// https://getbootstrap.com/
		return '<link rel="stylesheet" type="text/css" href="'.DOMAIN_CORE_VENDORS.'bootstrap/bootstrap.min.css?version='.BLUDIT_VERSION.'">'.PHP_EOL;
	}

	public static function cssBootstrapIcons()
	{
		// https://icons.getbootstrap.com/
		return '<link rel="stylesheet" type="text/css" href="'.DOMAIN_CORE_VENDORS.'bootstrap-icons/bootstrap-icons.css?version='.BLUDIT_VERSION.'">'.PHP_EOL;
	}

	public static function jsSortable($attributes='')
	{
		// https://github.com/psfpro/bootstrap-html5sortable
		return '<script '.$attributes.' src="'.DOMAIN_CORE_VENDORS.'bootstrap-html5sortable/jquery.sortable.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
	}

	/*	Generates a dynamiclly the meta tag title for the themes === Bludit v4

		@return				string		Returns the meta tag title <title>...</title>
	*/
	public static function metaTagTitle()
	{
		global $url;
		global $site;
		global $page;
		global $WHERE_AM_I;

		if ($WHERE_AM_I=='page') {
			$format = $site->titleFormatPages();
			$format = Text::replace('{{page-title}}', $page->title(), $format);
			$format = Text::replace('{{page-description}}', $page->description(), $format);
		} elseif ($WHERE_AM_I=='tag') {
			try {
				$tagKey = $url->slug();
				$tag = new Tag($tagKey);
				$format = $site->titleFormatTag();
				$format = Text::replace('{{tag-name}}', $tag->name(), $format);
			} catch (Exception $e) {
				// Tag doesn't exist
			}
		} elseif ($WHERE_AM_I=='category') {
			try {
				$categoryKey = $url->slug();
				$category = new Category($categoryKey);
				$format = $site->titleFormatCategory();
				$format = Text::replace('{{category-name}}', $category->name(), $format);
			} catch (Exception $e) {
				// Category doesn't exist
			}
		} else {
			$format = $site->titleFormatHomepage();
		}

		$format = Text::replace('{{site-title}}', $site->title(), $format);
		$format = Text::replace('{{site-slogan}}', $site->slogan(), $format);
		$format = Text::replace('{{site-description}}', $site->description(), $format);

		return '<title>'.$format.'</title>'.PHP_EOL;
	}

	/*	Generates a dynamiclly the meta tag description for the themes === Bludit v4

		@return				string		Returns the meta tag title <meta name="description" content="">
	*/
	public static function metaTagDescription()
	{
		global $site;
		global $page;
		global $url;
		global $WHERE_AM_I;

		$description = $site->description();

		if ($WHERE_AM_I=='page') {
			$description = $page->description();
		} elseif ($WHERE_AM_I=='category') {
			try {
				$categoryKey = $url->slug();
				$category = new Category($categoryKey);
				$description = $category->description();
			} catch (Exception $e) {
				// description from the site
			}
		}

		return '<meta name="description" content="'.$description.'">'.PHP_EOL;
	}

	/*	Returns the short version of the current languages of the site === Bludit v4

		@return				string		Returns the language sort version, for example: "en" or "de"
	*/
	public static function lang()
	{
		global $language;
		return $language->currentLanguageShortVersion();
	}

	// --- CHECK OLD

	public static function charset($charset)
	{
		return '<meta charset="'.$charset.'">'.PHP_EOL;
	}

	public static function viewport($content)
	{
		return '<meta name="viewport" content="'.$content.'">'.PHP_EOL;
	}

	public static function src($file, $base=DOMAIN_THEME)
	{
		return $base.$file;
	}

	public static function socialNetworks()
	{
		global $site;
		$socialNetworks = array(
			'github'=>'Github',
			'gitlab'=>'GitLab',
			'twitter'=>'Twitter',
			'facebook'=>'Facebook',
			'instagram'=>'Instagram',
			'codepen'=>'Codepen',
			'linkedin'=>'Linkedin',
			'xing'=>'Xing',
			'mastodon'=>'Mastodon',
			'vk'=>'VK'
		);

		foreach ($socialNetworks as $key=>$label) {
			if (!$site->{$key}()) {
				unset($socialNetworks[$key]);
			}
		}
		return $socialNetworks;
	}

	public static function title()
	{
		global $site;
		return $site->title();
	}

	public static function description()
	{
		global $site;
		return $site->description();
	}

	public static function slogan()
	{
		global $site;
		return $site->slogan();
	}

	public static function footer()
	{
		global $site;
		return $site->footer();
	}

	public static function rssUrl()
	{
		if (pluginActivated('pluginRSS')) {
			return DOMAIN_BASE.'rss.xml';
		}
		return false;
	}

	public static function sitemapUrl()
	{
		if (pluginActivated('pluginSitemap')) {
			return DOMAIN_BASE.'sitemap.xml';
		}
		return false;
	}

	// Returns the absolute URL of the site
	// Ex. https://example.com the method returns https://example.com/
	// Ex. https://example.com/bludit/ the method returns https://example.com/bludit/
	public static function siteUrl()
	{
		return DOMAIN_BASE;
	}

	// Returns the absolute URL of admin panel
	// Ex. https://example.com/admin/ the method returns https://example.com/admin/
	// Ex. https://example.com/bludit/admin/ the method returns https://example.com/bludit/admin/
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





}

?>