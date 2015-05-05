<?php defined('BLUDIT') or die('Bludit CMS.');

class Post extends fileContent
{
	function __construct($slug)
	{
		$this->path = PATH_POSTS;

		parent::__construct($slug);
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

	public function key()
	{
		return $this->getField('key');
	}

	public function username()
	{
		return $this->getField('username');
	}

	// Returns TRUE if the post is published, FALSE otherwise.
	public function published()
	{
		return ($this->getField('status')==='published');
	}

	public function author()
	{
		return $this->getField('author');
	}

	public function description()
	{
		return $this->getField('description');
	}

	public function unixTimeCreated()
	{
		return $this->getField('unixTimeCreated');
	}

	public function unixTimeModified()
	{
		return $this->getField('unixTimeModified');
	}

	public function date($format = false)
	{
		if($format!==false)
		{
			$unixTimeCreated = $this->unixTimeCreated();
			return Date::format($unixTimeCreated, $format);
		}

		return $this->getField('date');
	}

	public function timeago()
	{
		return $this->getField('timeago');
	}

	public function tags()
	{
		return $this->getField('tags');
	}

	public function slug()
	{
		return $this->getField('key');
	}

	public function permalink()
	{
		global $Url;

		$filter = ltrim($Url->filters('post'), '/');

		if($Url->filters('post')==HTML_PATH_ROOT)
			return HTML_PATH_ROOT.$this->slug();

		return HTML_PATH_ROOT.$filter.$this->slug();
	}

}
