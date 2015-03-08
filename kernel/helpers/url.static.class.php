<?php defined('BLUDIT') or die('Bludit CMS.');

class helperUrl
{
	private static $uri;
	private static $uri_strlen;
	private static $whereAmI;
	private static $slug;
	private static $filters; // Filters for the URI
	private static $notFound;

	// Filters may be changed for different languages
	// Ex (Spanish): Array('post'=>'/publicacion/', 'tag'=>'/etiqueta/', ....)
	// Ex (English): Array('post'=>'/post/', 'tag'=>'/tag/', ....)
	public static function init($filters)
	{
		// Decodes any %## encoding in the given string. Plus symbols ('+') are decoded to a space character.
		self::$uri = urldecode($_SERVER['REQUEST_URI']);

		// URI Lowercase
		//self::$uri = helperText::lowercase(self::$uri);

		self::$uri_strlen = helperText::length(self::$uri);

		self::$whereAmI = 'home';

		self::$notFound = false;

		self::$slug = false;

		self::$filters = $filters;

		// Check if filtering by post
		// Primero verifico que no haya ningun slug filtrado, asi no lo piso.
		if(self::$slug===false)
			self::is_post($filters['post']);

		// Check if filtering by tag
		// Primero verifico que no haya ningun slug filtrado, asi no lo piso.
		if(self::$slug===false)
			self::is_tag($filters['tag']);

		// Check if filtering by page
		// Primero verifico que no haya ningun slug filtrado, asi no lo piso.
		if(self::$slug===false)
			self::is_page($filters['page']);
	}

	public static function slug()
	{
		return self::$slug;
	}

	public static function uri()
	{
		return self::$uri;
	}

	// Return the filter used
	public static function filters($type)
	{
		return self::$filters[$type];
	}

	// Return: home, tag, post
	public static function whereAmI()
	{
		return self::$whereAmI;
	}

	public static function setWhereAmI($where)
	{
		self::$whereAmI = $where;
	}


	public static function notFound()
	{
		return self::$notFound;
	}

	public static function setNotFound($error = true)
	{
		self::$whereAmI = 'page';
		self::$notFound = $error;
	}

	public static function is_tag($filter)
	{
		if(self::filter_slug($filter)===false)
			return false;

		self::$whereAmI = 'tag';

		return true;
	}

	public static function is_post($filter)
	{
		if(self::filter_slug($filter)===false)
			return false;

		self::$whereAmI = 'post';

		return true;
	}

	public static function is_page($filter)
	{
		if(self::filter_slug($filter)===false)
			return false;

		self::$whereAmI = 'page';

		return true;
	}

	// Return the slug after the $filter
	// ex: http://domain.com/cms/$filter/slug123 => slug123
	private static function filter_slug($filter)
	{
		$position = helperText::strpos(self::$uri, $filter);

		if($position===false)
			return false;

		$start = $position + helperText::length($filter);
		$end = self::$uri_strlen;

		self::$slug = helperText::cut(self::$uri, $start, $end);

		if(empty(self::$slug))
			return false;

		return self::$slug;
	}

}

?>
