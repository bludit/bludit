<?php defined('BLUDIT') or die('Bludit CMS.');

class Url
{
	protected string $uri;
	protected int $uriStrlen;
	protected string $whereAmI;
	protected string $slug;
	protected array $filters; // Filters for the URI
	protected bool $notFound;
	protected array $parameters;
	protected string $activeFilter;
	protected int $httpCode;
	protected string $httpMessage;

	function __construct()
	{
		// Decodes any %## encoding in the given string. Plus symbols ('+') are decoded to a space character.
		$decode = urldecode($_SERVER['REQUEST_URI']);

		// Remove parameters GET, I don't use parse_url because has problem with utf-8
		$explode = explode('?', $decode);
		$this->uri = $explode[0];
		$this->parameters = $_GET;
		$this->uriStrlen = Text::length($this->uri);
		$this->whereAmI = 'home';
		$this->notFound = false;
		$this->slug = '';
		$this->filters = array();
		$this->activeFilter = '';
		$this->httpCode = 200;
		$this->httpMessage = 'OK';
	}

	// Filters change for different languages
	// Ex (Spanish): Array('post'=>'/publicacion/', 'tag'=>'/etiqueta/', ....)
	// Ex (English): Array('post'=>'/post/', 'tag'=>'/tag/', ....)
	public function checkFilters($filters): bool
	{
		// Put the "admin" filter first
		$adminFilter['admin'] = $filters['admin'];
		unset($filters['admin']);
		uasort($filters, array($this, 'sortByLength'));
		$this->filters = $adminFilter + $filters;

		foreach ($this->filters as $filterName=>$filterURI) {
			// $filterName = 'category'
			// $filterURI = '/category/'
			// $filterURIwoSlash = '/category'
			$filterURIwoSlash = rtrim($filterURI, '/');

			// $filterFull = '/base_url/category/'
			$filterFull = ltrim($filterURI, '/');
			$filterFull = HTML_PATH_ROOT.$filterFull;
			$filterFullLength = Text::length($filterFull);

			$subString = mb_substr($this->uri, 0, $filterFullLength, CHARSET);

			// Check coincidence without the last slash at the end, this case is not found
			if ($subString==$filterURIwoSlash) {
				$this->setNotFound();
				return false;
			}

			// Check coincidence with complete filterURI
			if ($subString==$filterFull) {
				$this->slug = mb_substr($this->uri, $filterFullLength);
				$this->setWhereAmI($filterName);
				$this->activeFilter = $filterURI;

				if (empty($this->slug) && ($filterName=='blog')) {
					$this->setWhereAmI('blog');
				} elseif (!empty($this->slug) && ($filterName=='blog')) {
					$this->setNotFound();
					return false;
				} elseif (empty($this->slug) && ($filterURI=='/')) {
					$this->setWhereAmI('home');
				} elseif (!empty($this->slug) && ($filterURI=='/')) {
					$this->setWhereAmI('page');
				} elseif ($filterName=='admin') {
					$this->slug = ltrim($this->slug, '/');
				}

				return true;
			}
		}

		$this->setNotFound();
		return false;
	}

	public function slug(): string
	{
		return $this->slug;
	}

	public function setSlug($slug): void
	{
		$this->slug = $slug;
	}

	public function activeFilter(): string
	{
		return $this->activeFilter;
	}

	public function explodeSlug($delimiter="/")
	{
		return explode($delimiter, $this->slug);
	}

	public function uri(): string
	{
		return $this->uri;
	}

	// Return the filter filter by type
	public function filters($type, $trim=true)
	{
		$filter = $this->filters[$type];
		if ($trim) {
			$filter = trim($filter, '/');
		}
		return $filter;
	}

	// Returns where is the user, home, pages, categories, tags..
	public function whereAmI(): string
	{
		return $this->whereAmI;
	}

	public function setWhereAmI($where): void
	{
		$GLOBALS['WHERE_AM_I'] = $where;
		$this->whereAmI = $where;
	}

	public function notFound(): bool
	{
		return $this->notFound;
	}

	public function pageNumber(): int
	{
		if (isset($this->parameters['page'])) {
			return (int)$this->parameters['page'];
		}
		return 1;
	}

	public function parameter($field)
	{
		if (isset($this->parameters[$field])) {
			return $this->parameters[$field];
		}
		return false;
	}

	public function setNotFound(): void
	{
		$this->setWhereAmI('page');
		$this->notFound = true;
		$this->httpCode = 404;
		$this->httpMessage = 'Not Found';
	}

	public function httpCode(): int
	{
		return $this->httpCode;
	}

	public function setHttpCode($code = 200): void
	{
		$this->httpCode = $code;
	}

	public function httpMessage(): string
	{
		return $this->httpMessage;
	}

	public function setHttpMessage($msg = 'OK'): void
	{
		$this->httpMessage = $msg;
	}

	private function sortByLength($a, $b)
	{
		return strlen($b)-strlen($a);
	}

}
