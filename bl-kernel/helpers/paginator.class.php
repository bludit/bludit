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

	public static function html($textPrevPage=false, $textNextPage=false, $showPageNumber=false)
	{
		global $L;

		$html  = '<div id="paginator">';
		$html .= '<ul>';

		if(self::get('showNext'))
		{
			if($textPrevPage===false) {
				$textPrevPage = '« '.$L->g('Previous page');
			}

			$html .= '<li class="left">';
			$html .= '<a href="'.self::nextPageUrl().'">'.$textPrevPage.'</a>';
			$html .= '</li>';
		}

		if($showPageNumber) {
			$html .= '<li class="list">'.(self::get('currentPage')+1).' / '.(self::get('numberOfPages')+1).'</li>';
		}

		if(self::get('showPrev'))
		{
			if($textNextPage===false) {
				$textNextPage = $L->g('Next page').' »';
			}

			$html .= '<li class="right">';
			$html .= '<a href="'.self::previousPageUrl().'">'.$textNextPage.'</a>';
			$html .= '</li>';
		}

		$html .= '</ul>';
		$html .= '</div>';

		return $html;
	}

	/*
	 * Bootstrap Pagination
	 */
	public static function bootstrap_html($textPrevPage=false, $textNextPage=false, $showPageNumber=false){

		global $language;

		$total_pages = self::numberOfPages();
		$howMany = 2;
		$currentPage = self::currentPage();
		$first_page = self::firstPage();
		$last_page = self::lastPageUrl();
		$show_next = (self::showNext())  ? "" : "disabled";
		$show_previois = (self::showPrev()) ? "" : "disabled";

		$html = '<nav aria-label="Page navigation">';
		$html .= '<ul class="pagination">';
		if ($currentPage > 3 || $currentPage === $total_pages){
			$html .= '<li class="page-item">';
			$html .= '<a class="page-link" href="'.self::firstPageUrl().'" aria-label="First"><span aria-hidden="true">&laquo;</span> '.$language->get('First').'</a>';
			$html .= '</li>';
		}
		if ($currentPage > 1){
			$html .= '<li class="page-item'.$show_previois.'">';
			$html .= '<a class="page-link" href="'.self::previousPageUrl().'" aria-label="Previous"><span aria-hidden="true">&laquo;</span> '.$language->get('Previous').'</a>';
			$html .= '</li>';
		}
		if ($currentPage > $howMany + 1){
			$html .= '<li class="page-item disabled"><span>...</span></li>';
		}
		for ($pageIndex = $currentPage - $howMany; $pageIndex <= $currentPage + $howMany; $pageIndex++){

			$active = ($pageIndex==self::currentPage()) ? "active" : false;

			if ($pageIndex >= 1 && $pageIndex <= $total_pages){
				$html .= '<li class ="'.$active.'"><a href="'.self::numberUrl($pageIndex).'">'.$pageIndex.'</a></li>';
			}
		}
		if ($currentPage < $total_pages){
			$html .= '<li class="page-item disabled"><span>...</span></li>';
		}
		if ($currentPage < $total_pages){
			$html .= '<li class="page-item'.$show_next.'">';
			$html .= '<a class="page-link" href="'.self::nextPageUrl().'" aria-label="Next">'.$language->get('Next').' <span aria-hidden="true">&raquo;</span></a>';
			$html .= '</li>';
			$html .= '<li><a href="'.$last_page.'">'.$language->get('Last').'</a></li>';
		}
		$html .= '</ul>';
		$html .= '</nav>';

		return $html;

	}

}
