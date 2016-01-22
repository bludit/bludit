<?php defined('BLUDIT') or die('Bludit CMS.');

class Paginator {

	public static $pager = array(
		'numberOfPostsAndDraft'=>0,
		'numberOfPosts'=>0,
		'numberOfPages'=>0,
		'nextPage'=>0,
		'prevPage'=>0,
		'currentPage'=>0,
		'showOlder'=>false,
		'showNewer'=>false,
		'show'=>false
	);

	public static function set($key, $value)
	{
		self::$pager[$key] = $value;
	}

	public static function get($key)
	{
		return self::$pager[$key];
	}

	public static function urlNextPage()
	{
		global $Url;

		$domain = trim(DOMAIN_BASE,'/');
		$filter = trim($Url->activeFilter(), '/');

		if(empty($filter)) {
			$url = $domain.'/'.$Url->slug();
		}
		else {
			$url = $domain.'/'.$filter.'/'.$Url->slug();
		}

		return $url.'?page='.self::get('nextPage');
	}

	public static function urlPrevPage()
	{
		global $Url;

		$domain = trim(DOMAIN_BASE,'/');
		$filter = trim($Url->activeFilter(), '/');

		if(empty($filter)) {
			$url = $domain.'/'.$Url->slug();
		}
		else {
			$url = $domain.'/'.$filter.'/'.$Url->slug();
		}

		return $url.'?page='.self::get('prevPage');
	}

	public static function html($textPrevPage=false, $textNextPage=false, $showPageNumber=false)
	{
		global $Language;

		$html  = '<div id="paginator">';
		$html .= '<ul>';

		if(self::get('showNewer'))
		{
			if($textPrevPage===false) {
				$textPrevPage = '« '.$Language->g('Prev page');
			}

			$html .= '<li class="left">';
			$html .= '<a href="'.self::urlPrevPage().'">'.$textPrevPage.'</a>';
			$html .= '</li>';
		}

		if($showPageNumber) {
			$html .= '<li class="list">'.(self::get('currentPage')+1).' / '.(self::get('numberOfPages')+1).'</li>';
		}

		if(self::get('showOlder'))
		{
			if($textNextPage===false) {
				$textNextPage = $Language->g('Next page').' »';
			}

			$html .= '<li class="right">';
			$html .= '<a href="'.self::urlNextPage().'">'.$textNextPage.'</a>';
			$html .= '</li>';
		}

		$html .= '</ul>';
		$html .= '</div>';

		return $html;
	}

}