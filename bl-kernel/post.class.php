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

	// Returns the post slug
	public function slug()
	{
		return $this->getField('key');
	}



}