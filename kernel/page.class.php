<?php defined('BLUDIT') or die('Bludit CMS.');

class Page extends Content {

	function __construct($key)
	{
		// Database Key
		$this->setField('key', $key);

		// Set filterType
		$this->setField('filterType', 'page');

		parent::__construct(PATH_PAGES.$key.DS);
	}

	// Returns the page position.
	public function position()
	{
		return $this->getField('position');
	}

	// Returns the page slug.
	public function slug()
	{
		$explode = explode('/', $this->getField('key'));

		// Check if the page have a parent.
		if(!empty($explode[1])) {
			return $explode[1];
		}

		return $explode[0];
	}

	// Returns the parent key, if the page doesn't have a parent returns FALSE.
	public function parentKey()
	{
		$explode = explode('/', $this->getField('key'));
		if(isset($explode[1])) {
			return $explode[0];
		}

		return false;
	}

	// Returns the parent method output, if the page doesn't have a parent returns FALSE.
	public function parentMethod($method)
	{
		global $pages;

		if( isset($pages[$this->parentKey()]) ) {
			return $pages[$this->parentKey()]->{$method}();
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

}