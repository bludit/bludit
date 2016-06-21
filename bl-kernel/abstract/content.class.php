<?php defined('BLUDIT') or die('Bludit CMS.');

class Content {

	public $vars;

	function __construct($path)
	{
		if($this->build($path)===false) {
			$this->vars = false;
		}
	}

	// Return TRUE if the content is loaded correctly
	public function isValid()
	{
		return($this->vars!==false);
	}

	// Returns the value from the $field, FALSE if the field doesn't exist
	public function getField($field)
	{
		if(isset($this->vars[$field])) {
			return $this->vars[$field];
		}

		return false;
	}

	// Set a value to a field
	public function setField($field, $value, $overwrite=true)
	{
		if($overwrite || empty($this->vars[$field])) {
			$this->vars[$field] = $value;
		}

		return true;
	}

	// Parse the content from the file index.txt
	private function build($path)
	{
		if( !Sanitize::pathFile($path.'index.txt') ) {
			return false;
		}

		$tmp = 0;
		$lines = file($path.'index.txt');
		foreach($lines as $lineNumber=>$line)
		{
			$parts = array_map('trim', explode(':', $line, 2));

			// Lowercase variable
			$parts[0] = Text::lowercase($parts[0]);

			// If variables is content then break the foreach and process the content after.
			if($parts[0]==='content')
			{
				$tmp = $lineNumber;
				break;
			}

			if( !empty($parts[0]) && !empty($parts[1]) ) {
				// Sanitize all fields, except Content.
				$this->vars[$parts[0]] = Sanitize::html($parts[1]);
			}
		}

		// Process the content.
		if($tmp!==0)
		{
			// Next line after "Content:" variable
			$tmp++;

			// Remove lines after Content
			$output = array_slice($lines, $tmp);

			if(!empty($parts[1])) {
				array_unshift($output, "\n");
				array_unshift($output, $parts[1]);
			}

			$implode = implode($output);
			$this->vars['content'] = $implode;

			// Sanitize content.
			//$this->vars['content'] = Sanitize::html($implode);
		}

	}

	// Returns the title field
	public function title()
	{
		return $this->getField('title');
	}

	// Returns the content
	// This content is markdown parser
	// (boolean) $fullContent, TRUE returns all content, if FALSE returns the first part of the content
	// (boolean) $noSanitize, TRUE returns the content without sanitized
	public function content($fullContent=true, $noSanitize=true)
	{
		// This content is not sanitized.
		$content = $this->getField('content');

		if(!$fullContent) {
			$content = $this->getField('breakContent');
		}

		if($noSanitize) {
			return $content;
		}

		return Sanitize::html($content);
	}

	// Returns the content
	// This content is not markdown parser
	// (boolean) $noSanitize, TRUE returns the content without sanitized
	public function contentRaw($noSanitize=true)
	{
		// This content is not sanitized.
		$content = $this->getField('contentRaw');

		if($noSanitize) {
			return $content;
		}

		return Sanitize::html($content);
	}

	// Returns TRUE if the content has the text splited
	public function readMore()
	{
		return $this->getField('readMore');
	}

	// Returns the field key
	public function key()
	{
		return $this->getField('key');
	}

	// Returns TRUE if the post/page is published, FALSE otherwise.
	public function published()
	{
		return ($this->getField('status')==='published');
	}

	// Returns TRUE if the post/page is scheduled, FALSE otherwise.
	public function scheduled()
	{
		return ($this->getField('status')==='scheduled');
	}

	// Returns TRUE if the post/page is draft, FALSE otherwise.
	public function draft()
	{
		return ($this->getField('status')=='draft');
	}

	// Returns the file name of the cover image, FALSE there isn't a cover image setted
	// (boolean) $absolute, TRUE returns the absolute path and file name, FALSE just the file name
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

/*
	DEPRECATED ?

	public function profilePicture()
	{
		return HTML_PATH_UPLOADS_PROFILES.$this->username().'.jpg';
	}
*/
	// Returns the user object
	// (boolean) $field, TRUE returns the value of the field, FALSE returns the object
	public function user($field=false)
	{
		// Get the user object.
		$User = $this->getField('user');

		if($field) {
			return $User->getField($field);
		}

		return $User;
	}

	// Returns the username who created the post/page
	public function username()
	{
		return $this->getField('username');
	}

	// Returns the description field
	public function description()
	{
		return $this->getField('description');
	}

	// Returns the date according to locale settings and format settings
	public function date()
	{
		return $this->getField('date');
	}

	// Returns the date according to locale settings and format as database stored
	// (string) $format, you can specify the date format
	public function dateRaw($format=false)
	{
		$date = $this->getField('dateRaw');

		if($format) {
			return Date::format($date, DB_DATE_FORMAT, $format);
		}

		return $date;
	}

	// Returns the tags
	// (boolean) $returnsArray, TRUE to get the tags as an array, FALSE to get the tags separeted by comma
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

	// Returns the permalink
	// (boolean) $absolute, TRUE returns the post/page link with the DOMAIN, FALSE without the DOMAIN
	public function permalink($absolute=false)
	{
		global $Url;
		global $Site;

		$filterType = $this->getField('filterType');

		$url = trim(DOMAIN_BASE,'/');
		$key = $this->key();
		$filter = trim($Url->filters($filterType), '/');
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

	public function json($returnsArray=false)
	{
		$tmp['key'] 		= $this->key();
		$tmp['title'] 		= $this->title();
		$tmp['content'] 	= $this->content(); // Markdown parsed
		$tmp['contentRaw'] 	= $this->contentRaw(); // No Markdown parsed
		$tmp['description'] 	= $this->description();
		$tmp['date'] 		= $this->dateRaw();
		$tmp['permalink'] 	= $this->permalink(true);

		if($returnsArray) {
			return $tmp;
		}

		return json_encode($tmp);
	}
}