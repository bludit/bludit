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

}

?>