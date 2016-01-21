<?php defined('BLUDIT') or die('Bludit CMS.');

class Post extends Content {

	function __construct($key)
	{
		// Database Key
		$this->setField('key', $key);

		// Set filterType
		$this->setField('filterType', 'post');

		parent::__construct(PATH_POSTS.$key.DS);
	}

	public function key()
	{
		return $this->getField('key');
	}

	public function slug()
	{
		return $this->getField('key');
	}

	// Returns TRUE if the post is scheduled, FALSE otherwise.
	public function scheduled()
	{
		return ($this->getField('status')==='scheduled');
	}
}