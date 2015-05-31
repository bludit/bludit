<?php defined('BLUDIT') or die('Bludit CMS.');

class Url
{
	private $uri;
	private $uriStrlen;
	private $whereAmI;
	private $slug;
	private $filters; // Filters for the URI
	private $notFound;
	private $parameters;

	function __construct()
	{
		// Decodes any %## encoding in the given string. Plus symbols ('+') are decoded to a space character.
		$decode = urldecode($_SERVER['REQUEST_URI']);

		// Parse, http://php.net/parse_url
		$parse = parse_url($decode);

		$this->uri = $parse['path'];

		$this->parameters = $_GET;

		$this->uriStrlen = Text::length($this->uri);

		$this->whereAmI = 'home';

		$this->notFound = false;

		$this->slug = false;

		$this->filters = array();
	}

	// Filters may be changed for different languages
	// Ex (Spanish): Array('post'=>'/publicacion/', 'tag'=>'/etiqueta/', ....)
	// Ex (English): Array('post'=>'/post/', 'tag'=>'/tag/', ....)
	public function checkFilters($filters)
	{
		// Get the admin filter
		$adminFilter['admin'] = $filters['admin'];
		unset($filters['admin']);

		// Sort by filter length
		uasort($filters, array($this, 'sortByLength'));

		// Push the admin filter first
		$filters = $adminFilter + $filters;

		$this->filters = $filters;

		foreach($filters as $filterKey=>$filter)
		{
			$slug = $this->getSlugAfterFilter($filter);
			if($slug!==false)
			{
				if(empty($slug))
				{
					if($filter==='/')
					{
						$this->whereAmI = 'home';
						break;
					}

					$this->setNotFound(true);
				}

				$this->whereAmI = $filterKey;
				break;
			}
		}
	}

	public function slug()
	{
		return $this->slug;
	}

	public function explodeSlug($delimiter="/")
	{
		return explode($delimiter, $this->slug);
	}

	public function uri()
	{
		return $this->uri;
	}

	// Return the filter used
	public function filters($type)
	{
		return $this->filters[$type];
	}

	// Return: home, tag, post
	public function whereAmI()
	{
		return $this->whereAmI;
	}

	public function setWhereAmI($where)
	{
		$this->whereAmI = $where;
	}

	public function notFound()
	{
		return $this->notFound;
	}

	public function pageNumber()
	{
		if(isset($this->parameters['page'])) {
			return $this->parameters['page'];
		}
		return 0;
	}

	public function setNotFound($error = true)
	{
		$this->notFound = $error;
	}

	public function getDomain()
	{
		if(!empty($_SERVER['HTTPS'])) {
			$protocol = 'https://';
		}
		else {
			$protocol = 'http://';
		}

		$domain = $_SERVER['HTTP_HOST'];

		return $protocol.$domain.HTML_PATH_ROOT;
	}

	// Return the slug after the $filter
	// If the filter is not contain in the uri, returns FALSE
	// If the filter is contain in the uri and the slug is not empty, returns the slug
	// ex: http://domain.com/cms/$filter/slug123 => slug123
	private function getSlugAfterFilter($filter)
	{
		if($filter=='/') {
			$filter = HTML_PATH_ROOT;
		}

		// Check if the filter is in the uri.
		$position = Text::strpos($this->uri, $filter);
		if($position===false) {
			return false;
		}

		$start = $position + Text::length($filter);
		$end = $this->uriStrlen;

		$slug = Text::cut($this->uri, $start, $end);
		$this->slug = trim($slug, '/');

		return $slug;
	}

	private function sortByLength($a, $b)
	{
		return strlen($b)-strlen($a);
	}

}
