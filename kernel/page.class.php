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

	public function tagsArray()
	{
		$tags = $this->getField('tags');
		return explode(',', $tags);
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
	public function dateCreated($format=false)
	{
		if($format===false) {
			return $this->getField('date');
		}

		$unixTimeCreated = $this->unixTimeCreated();

		return Date::format($unixTimeCreated, $format);
	}

	public function dateModified($format=false)
	{
		if($format===false) {
			return $this->getField('date');
		}

		$unixTimeModified = $this->unixTimeModified();

		return Date::format($unixTimeModified, $format);
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
	public function permalink($absolute=false)
	{
		global $Url;
		global $Site;

		$url = trim($Site->url(),'/');
		$key = $this->key();
		$filter = trim($Url->filters('page'), '/');
		$htmlPath = trim(HTML_PATH_ROOT,'/');

		if(empty($filter)) {
			$tmp = $key;
		}
		else {
			$tmp = $filter.'/'.$key;
		}

		if($absolute) {
			return $url.'/'.$tmp;
		}

		return '/'.$htmlPath.'/'.$tmp;
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
		$paths = glob(PATH_PAGES.$this->getField('key').DS.'*', GLOB_ONLYDIR);
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
