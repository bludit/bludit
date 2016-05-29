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

	public function json()
	{
		$tmp['key'] 		= $this->key();
		$tmp['title'] 		= $this->title();
		$tmp['content'] 	= $this->content(); // Markdown parsed
		$tmp['contentRaw'] 	= $this->contentRaw(); // No Markdown parsed
		$tmp['description'] 	= $this->description();

		return json_encode($tmp);
	}
}