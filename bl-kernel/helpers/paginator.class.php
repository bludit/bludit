<?php defined('BLUDIT') or die('Bludit CMS.');

class Paginator {

	public static $pager = array(
		'itemsPerPage'=>0,
		'numberOfPages'=>1,
		'numberOfItems'=>0,
		'firstPage'=>1,
		'nextPage'=>1,
		'prevPage'=>1,
		'currentPage'=>1,
		'showPrev'=>false,
		'showNext'=>false,
		'showNextPrev'=>false
	);

	public static function set($key, $value)
	{
		self::$pager[$key] = $value;
	}

	public static function get($key)
	{
		return self::$pager[$key];
	}

	public static function numberOfPages()
	{
		return self::get('numberOfPages');
	}

	public static function currentPage()
	{
		return self::get('currentPage');
	}

	public static function nextPage()
	{
		return self::get('nextPage');
	}

	public static function prevPage()
	{
		return self::get('prevPage');
	}

	public static function showNext()
	{
		return self::get('showNext');
	}

	public static function showPrev()
	{
		return self::get('showPrev');
	}

	public static function firstPage()
	{
		return self::get('firstPage');
	}

	// Returns the absolute URL for the first page
	public static function firstPageUrl()
	{
		return self::numberUrl( self::firstPage() );
	}

	// Returns the absolute URL for the last page
	public static function lastPageUrl()
	{
		return self::numberUrl( self::numberOfPages() );
	}

	// Returns the absolute URL for the next page
	public static function nextPageUrl()
	{
		return self::numberUrl( self::nextPage() );
	}

	// Returns the absolute URL for the previous page
	public static function previousPageUrl()
	{
		return self::numberUrl( self::prevPage() );
	}

	// Return the absoulte URL with the page number
	public static function numberUrl($pageNumber)
	{
		global $url;

		$domain = trim(DOMAIN_BASE,'/');
		$filter = trim($url->activeFilter(), '/');

		if(empty($filter)) {
			$uri = $domain.'/'.$url->slug();
		}
		else {
			$uri = $domain.'/'.$filter.'/'.$url->slug();
		}

		return $uri.'?page='.$pageNumber;
	}

}
