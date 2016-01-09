<?php defined('BLUDIT') or die('Bludit CMS.');

class Post extends fileContent
{
	function __construct($key)
	{
		// Database Key
		$this->setField('key', $key);

		parent::__construct(PATH_POSTS.$key.DS);
	}

	// Returns the post title.
	public function title()
	{
		return $this->getField('title');
	}

	// Returns the content.
	// This content is markdown parser.
	// (boolean) $fullContent, TRUE returns all content, if FALSE returns the first part of the content.
	// (boolean) $raw, TRUE returns the content without sanitized, FALSE otherwise.
	public function content($fullContent=true, $raw=true)
	{
		// This content is not sanitized.
		$content = $this->getField('content');

		if(!$fullContent) {
			$content = $this->getField('breakContent');
		}

		if($raw) {
			return $content;
		}

		return Sanitize::html($content);
	}

	public function readMore()
	{
		return $this->getField('readMore');
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

	public function key()
	{
		return $this->getField('key');
	}

	// Returns TRUE if the post is published, FALSE otherwise.
	public function published()
	{
		return ($this->getField('status')==='published');
	}

	// Returns TRUE if the post is scheduled, FALSE otherwise.
	public function scheduled()
	{
		return ($this->getField('status')==='scheduled');
	}

	// Returns TRUE if the post is draft, FALSE otherwise.
	public function draft()
	{
		return ($this->getField('status')=='draft');
	}

	public function coverImage($absolute=true)
	{
		$fileName = $this->getField('coverImage');

		if(empty($fileName)) {
			return false;
		}

		if($absolute) {
			return HTML_PATH_UPLOADS.$fileName;
		}

		return $fileName;
	}

	public function profilePicture()
	{
		return HTML_PATH_UPLOADS_PROFILES.$this->username().'.jpg';
	}

	// Returns the user object if $field is false, otherwise returns the field's value.
	public function user($field=false)
	{
		// Get the user object.
		$User = $this->getField('user');

		if($field) {
			return $User->getField($field);
		}

		return $User;
	}

	// DEPRECATED
	public function username()
	{
		return $this->getField('username');
	}

	// DEPRECATED
	public function authorFirstName()
	{
		return $this->getField('authorFirstName');
	}

	// DEPRECATED
	public function authorLastName()
	{
		return $this->getField('authorLastName');
	}

	public function description()
	{
		return $this->getField('description');
	}

	// Returns the post date according to locale settings and format settings.
	public function date()
	{
		return $this->getField('date');
	}

	// Returns the post date according to locale settings and format as database stored.
	public function dateRaw($format=false)
	{
		$date = $this->getField('dateRaw');

		if($format) {
			return Date::format($date, DB_DATE_FORMAT, $format);
		}

		return $date;
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

	public function slug()
	{
		return $this->getField('key');
	}

	public function permalink($absolute=false)
	{
		global $Url;
		global $Site;

		$url = trim($Site->url(),'/');
		$key = $this->key();
		$filter = trim($Url->filters('post'), '/');
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

}
