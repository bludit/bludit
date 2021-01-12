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
		return '<script src="'.DOMAIN_CORE_JS.'jquery.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
	}

	public static function jsBootstrap($attributes='')
	{
		return '<script '.$attributes.' src="'.DOMAIN_CORE_JS.'bootstrap.bundle.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
	}

	public static function cssBootstrap()
	{
		return '<link rel="stylesheet" type="text/css" href="'.DOMAIN_CORE_CSS.'bootstrap.min.css?version='.BLUDIT_VERSION.'">'.PHP_EOL;
	}

	public static function cssBootstrapIcons()
	{
		return '<link rel="stylesheet" type="text/css" href="'.DOMAIN_CORE_CSS.'bootstrap-icons/bootstrap-icons.css?version='.BLUDIT_VERSION.'">'.PHP_EOL;
	}

	public static function jsSortable($attributes='')
	{
		// https://github.com/psfpro/bootstrap-html5sortable
		return '<script '.$attributes.' src="'.DOMAIN_CORE_JS.'jquery.sortable.min.js?version='.BLUDIT_VERSION.'"></script>'.PHP_EOL;
	}

}

?>