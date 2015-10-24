<?php defined('BLUDIT') or die('Bludit CMS.');

class Page extends fileContent
{
	function __construct($key)
	{
		// Database Key
		$this->setField('key', $key);

		parent::__construct(PATH_PAGES.$key.DS);
	}

	// Returns the post title.
	public function title()
	{
		return $this->getField('title');
	}

	// Returns the content. This content is markdown parser.
	// (boolean) $html, TRUE returns the content without satinize, FALSE otherwise.
	public function content($html=true)
	{
		// This content is not sanitized.
		$content = $this->getField('content');

		if($html) {
			return $content;
		}

		return Sanitize::html($content);
	}

	// Returns the content. This content is not markdown parser.
	// (boolean) $raw, TRUE returns the content without sanitized, FALSE otherwise.
	public function contentRaw($raw=true)
	{
		// This content is not sanitized.
		$content = $this->getField('contentRaw');

		if($raw) {
			return $content;
		}

		return Sanitize::html($content);
	}

	public function description()
	{
		return $this->getField('description');
	}

	public function tags($returnsArray=false)
	{
		global $Url;

		$tags = $this->getField('tags');

		if($returnsArray) {

			if($tags==false) {
				return array();
			}

			return $tags;
		}
		else {
			if($tags==false) {
				return false;
			}

			// Return string with tags separeted by comma.
			return implode(', ', $tags);
		}
	}

	public function position()
	{
		return $this->getField('position');
	}

	// Returns the post date according to locale settings and format settings.
	public function date($format=false)
	{
		$date = $this->getField('date');

		if($format) {
			// En %d %b deberia ir el formato definido por el usuario
			return Date::format($date, DB_DATE_FORMAT, '%d %B');
		}

		return $date;
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

	// Returns TRUE if the post is draft, FALSE otherwise.
	public function draft()
	{
		return ($this->getField('status')=='draft');
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

		if(empty($htmlPath)) {
			return '/'.$tmp;
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
		//$paths = glob(PATH_PAGES.$this->getField('key').DS.'*', GLOB_ONLYDIR);
		$paths = Filesystem::listDirectories(PATH_PAGES.$this->getField('key').DS);
		foreach($paths as $path) {
			array_push($tmp, basename($path));
		}

		return $tmp;
	}

	public function username()
	{
		return $this->getField('username');
	}

	public function authorFirstName()
	{
		return $this->getField('authorFirstName');
	}

	public function authorLastName()
	{
		return $this->getField('authorLastName');
	}

}