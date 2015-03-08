<?php

class Post extends Content
{
	function __construct($slug)
	{
		$this->path = PATH_POSTS;

		parent::__construct($slug);
	}

	// Return the post title
	function title()
	{
		return $this->get_field('title');
	}

	// Return the post content
	function content()
	{
		return $this->get_field('content');
	}

	function username()
	{
		return $this->get_field('username');
	}

	// Return TRUE if the post is published, FALSE otherwise.
	function published()
	{
		return ($this->get_field('status')==='published');
	}

	function author()
	{
		return $this->get_field('author');
	}

	function unixstamp()
	{
		return $this->get_field('unixstamp');
	}

	function date($format = false)
	{
		if($format!==false)
		{
			$unixstamp = $this->unixstamp();
			return Date::format($unixstamp, $format);
		}

		return $this->get_field('date');
	}

	function timeago()
	{
		return $this->get_field('timeago');
	}

	function slug()
	{
		return $this->get_field('slug');
	}

	function permalink()
	{
		global $Url;
		
		$filter = ltrim($Url->filters('post'), '/');

		if($Url->filters('post')==HTML_PATH_ROOT)
			return HTML_PATH_ROOT.$this->slug();

		return HTML_PATH_ROOT.$filter.$this->slug();
	}

}

?>
