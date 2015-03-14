<?php defined('BLUDIT') or die('Bludit CMS.');

class Url
{
	private $uri;
	private $uri_strlen;
	private $whereAmI;
	private $slug;
	private $filters; // Filters for the URI
	private $notFound;

	function __construct()
	{
		// Decodes any %## encoding in the given string. Plus symbols ('+') are decoded to a space character.
		$this->uri = urldecode($_SERVER['REQUEST_URI']);

		// URI Lowercase
		//$this->uri = helperText::lowercase($this->uri);

		$this->uri_strlen = helperText::length($this->uri);

		$this->whereAmI = 'home';

		$this->notFound = false;

		$this->slug = false;
	}

	// Filters may be changed for different languages
	// Ex (Spanish): Array('post'=>'/publicacion/', 'tag'=>'/etiqueta/', ....)
	// Ex (English): Array('post'=>'/post/', 'tag'=>'/tag/', ....)
	public function checkFilters($filters)
	{
		$this->filters = $filters;

		// Check if filtering by post
		// Primero verifico que no haya ningun slug filtrado, asi no lo piso.
		if($this->slug===false)
			$this->is_post($filters['post']);

		// Check if filtering by tag
		// Primero verifico que no haya ningun slug filtrado, asi no lo piso.
		if($this->slug===false)
			$this->is_tag($filters['tag']);

		// Check if filtering by page
		// Primero verifico que no haya ningun slug filtrado, asi no lo piso.
		if($this->slug===false)
			$this->is_page($filters['page']);
	}

	public function slug()
	{
		return $this->slug;
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

	public function setNotFound($error = true)
	{
		$this->whereAmI = 'page';
		$this->notFound = $error;
	}

	public function is_tag($filter)
	{
		if($this->filter_slug($filter)===false)
			return false;

		$this->whereAmI = 'tag';

		return true;
	}

	public function is_post($filter)
	{
		if($this->filter_slug($filter)===false)
			return false;

		$this->whereAmI = 'post';

		return true;
	}

	public function is_page($filter)
	{
		if($this->filter_slug($filter)===false)
			return false;

		$this->whereAmI = 'page';

		return true;
	}

	// Return the slug after the $filter
	// ex: http://domain.com/cms/$filter/slug123 => slug123
	private function filter_slug($filter)
	{
		if($filter=='/')
			$filter = HTML_PATH_ROOT;

		$position = helperText::strpos($this->uri, $filter);

		if($position===false)
			return false;

		$start = $position + helperText::length($filter);
		$end = $this->uri_strlen;

		$this->slug = helperText::cut($this->uri, $start, $end);

		if(empty($this->slug))
			return false;

		return $this->slug;
	}

}

?>
