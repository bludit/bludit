<?php defined('BLUDIT') or die('Bludit CMS.');

class Page extends fileContent
{
	function __construct($key)
	{
		$this->path = PATH_PAGES;

		parent::__construct($key);
	}

	// Returns the post title.
	public function title()
	{
		return $this->getField('title');
	}

	// Returns the post content.
	public function content()
	{
		return $this->getField('content');
	}

	public function contentRaw()
	{
		return $this->getField('contentRaw');
	}

	public function description()
	{
		return $this->getField('description');
	}

	public function tags()
	{
		return $this->getField('tags');
	}

	public function position()
	{
		return $this->getField('position');
	}

	// Returns the post date in unix timestamp format, UTC-0.
	public function unixTimeCreated()
	{
		return $this->getField('unixTimeCreated');
	}

	public function unixTimeModified()
	{
		return $this->getField('unixTimeModified');
	}

	// Returns the post date according to locale settings and format settings.
	public function date($format = false)
	{
		if($format!==false)
		{
			$unixTimeCreated = $this->unixTimeCreated();
			return Date::format($unixTimeCreated, $format);
		}

		return $this->getField('date');
	}

	// Returns the time ago
	public function timeago()
	{
		return $this->getField('timeago');
	}

	// Returns the page slug.
	public function slug()
	{
		$explode = explode('/', $this->getField('key'));
		if(!empty($explode[1]))
			return $explode[1];

		return $explode[0];
	}

	public function key()
	{
		return $this->getField('key');
	}

	// Returns TRUE if the page is published, FALSE otherwise.
	public function published()
	{
		return ($this->getField('status')==='published');
	}

	// Returns the page permalink.
	public function permalink()
	{
		global $Url;

		$path = '';
		$slug = $this->slug();
		$parent = $this->parent();
		$filter = ltrim($Url->filters('page'), '/');

		if($Url->filters('page')==HTML_PATH_ROOT) {
			$path = HTML_PATH_ROOT;
		}
		else {
			$path = HTML_PATH_ROOT.$filter.'/';
		}

		if($parent===false) {
			return $path.$slug;
		}

		return $path.$parent.'/'.$slug;
	}

	public function parentKey()
	{
		$explode = explode('/', $this->getField('key'));
		if(isset($explode[1])) {
			return $explode[0];
		}

		return false;
	}

	public function children()
	{
		$tmp = array();
		$paths = glob(PATH_PAGES.$this->getField('key').'/*', GLOB_ONLYDIR);
		foreach($paths as $path) {
			array_push($tmp, basename($path));
		}

		return $tmp;
	}

	public function username()
	{
		return $this->getField('username');
	}

	public function author()
	{
		return $this->getField('author');
	}

}
