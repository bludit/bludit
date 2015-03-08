<?php

class Page extends Content
{
	function __construct($slug)
	{
		$this->path = PATH_PAGES;

		parent::__construct($slug);
	}

	// Returns the post title.
	function title()
	{
		return $this->get_field('title');
	}

	// Returns the post content.
	function content()
	{
		return $this->get_field('content');
	}

	// Returns the post date in unix timestamp format, UTC-0.
	function unixstamp()
	{
		return $this->get_field('unixstamp');
	}

	// Returns the post date according to locale settings and format settings.
	function date($format = false)
	{
		if($format!==false)
		{
			$unixstamp = $this->unixstamp();
			return Date::format($unixstamp, $format);
		}

		return $this->get_field('date');
	}

	// Returns the time ago
	function timeago()
	{
		return $this->get_field('timeago');
	}

	// Returns the page slug.
	function slug()
	{
		return $this->get_field('slug');
	}

	// Returns TRUE if the page is published, FALSE otherwise.
	function published()
	{
		return ($this->get_field('status')==='published');
	}

	// Returns the page permalink.
	function permalink()
	{
		global $Url;
		
		$filter = ltrim($Url->filters('page'), '/');

		if($Url->filters('page')==HTML_PATH_ROOT)
			return HTML_PATH_ROOT.$this->slug();

		return HTML_PATH_ROOT.$filter.$this->slug();
	}

	function parent()
	{
		if(!empty($this->get_field('parent')))
			return $this->get_field('parent');

		return false;
	}

	function username()
	{
		return $this->get_field('username');
	}

	function author()
	{
		return $this->get_field('author');
	}

}

?>
