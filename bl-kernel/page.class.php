<?php defined('BLUDIT') or die('Bludit CMS.');

class Page {

	private $vars;

	function __construct($key)
	{
		$this->vars = false;

		if ($this->build($key)) {
			$this->vars['key'] = $key;
		}
	}

	// Parse the content from the file index.txt
	private function build($key)
	{
		$filePath = PATH_PAGES.$key.DS.FILENAME;

		// Check if the file exists
		if (!Sanitize::pathFile($filePath)) {
			return false;
		}

		$tmp = 0;
		$file = file($filePath);
		foreach ($file as $lineNumber=>$line) {
			// Split the line in 2 parts, limiter by :
			$parts = explode(':', $line, 2);

			$field = $parts[0]; // title, date, slug
			$value = isset($parts[1])?$parts[1]:false; // value of title, value of date

			// Remove all characters except letters and dash - from field
			$field = preg_replace('/[^A-Za-z\-]/', '', $field);

			// Field to lowercase
			$field = Text::lowercase($field);

			// Check if the current line start the content of the page
			// We have two breakers, the word content or 3 dash ---
			if ($field==='content') {
				$tmp = $lineNumber;
				$styleTypeUsed = 'Content:';
				break;
			}

			if ($field==='---') {
				$tmp = $lineNumber;
				$styleTypeUsed = '---';
				break;
			}

			if (!empty($field) && !empty($value)) {
				// Remove missing dashs -
				$field = preg_replace('/[^A-Za-z]/', '', $field);

				// Remove <-- and -->
				$value = preg_replace('/<\-\-/', '', $value);
				$value = preg_replace('/\-\->/', '', $value);

				// Remove empty spaces on borders
				$value = trim($value);

				// Position accepts only integers
				if ($field=='position') {
					$value = preg_replace('/[^0-9]/', '', $value);
				}

				// Sanitize all fields, except the content
				$this->vars[$field] = Sanitize::html($value);
			}
		}

		// Process the content
		if ($tmp!==0) {
			// Get all lines starting from "Content:" or "---"
			$content = array_slice($file, $tmp);

			// Remove "Content:" or "---" and keep next characters if there are
			$content[0] = substr($content[0], strpos($content[0], $styleTypeUsed) + strlen($styleTypeUsed));

			$content[0] = ltrim($content[0]);

			// Join lines in one variable, this is RAW content from file
			$this->vars['contentRaw'] = implode($content);
		}

		return true;
	}

	// Returns TRUE if the content is loaded correctly, FALSE otherwise
	public function isValid()
	{
		return $this->vars!==false;
	}

	// DEPRACTED
	// Returns the value from the $field, FALSE if the field doesn't exist
	public function getField($field)
	{
		if(isset($this->vars[$field])) {
			return $this->vars[$field];
		}

		return false;
	}

	public function getValue($field)
	{
		if(isset($this->vars[$field])) {
			return $this->vars[$field];
		}

		return false;
	}

	public function getDB()
	{
		return $this->vars;
	}

	// Set a field with a value
	public function setField($field, $value, $overwrite=true)
	{
		if($overwrite || empty($this->vars[$field])) {
			$this->vars[$field] = $value;
		}

		return true;
	}

	// Returns the content
	// This content is markdown parser
	// (boolean) $fullContent, TRUE returns all content, if FALSE returns the first part of the content
	// (boolean) $noSanitize, TRUE returns the content without sanitized
	public function content($fullContent=true, $noSanitize=true)
	{
		// This content is not sanitized.
		$content = $this->getValue('content');

		if(!$fullContent) {
			return $this->contentBreak();
		}

		if($noSanitize) {
			return $content;
		}

		return Sanitize::html($content);
	}

	public function contentBreak()
	{
		return $this->getValue('contentBreak');
	}

	// Returns the raw content
	// This content is not markdown parser
	// (boolean) $noSanitize, TRUE returns the content without sanitized
	public function contentRaw($noSanitize=true)
	{
		// This content is not sanitized.
		$content = $this->getValue('contentRaw');

		if($noSanitize) {
			return $content;
		}

		return Sanitize::html($content);
	}

	// Returns the date according to locale settings and format settings
	public function date()
	{
		return $this->getValue('date');
	}

	// Returns the date according to locale settings and format as database stored
	// (string) $format, you can specify the date format
	public function dateRaw($format=false)
	{
		$date = $this->getValue('dateRaw');

		if($format) {
			return Date::format($date, DB_DATE_FORMAT, $format);
		}

		return $date;
	}

	// Returns the date according to locale settings and format settings
	public function dateModified()
	{
		return $this->getValue('dateModified');
	}

	// Returns the permalink
	// (boolean) $absolute, TRUE returns the page link with the DOMAIN, FALSE without the DOMAIN
	public function permalink($absolute=true)
	{
		// Get the key of the page
		$key = $this->getValue('key');

		if($absolute) {
			return DOMAIN_PAGES.$key;
		}

		return HTML_PATH_ROOT.PAGE_URI_FILTER.$key;
	}

	// Returns the category name
	public function category()
	{
		return $this->categoryMap('name');
	}

	// Returns the category key
	public function categoryKey()
	{
		return $this->getValue('category');
	}

	// Returns the field from the array
	// categoryMap = array( 'name'=>'', 'list'=>array() )
	public function categoryMap($field)
	{
		$map = $this->getValue('categoryMap');

		if($field=='key') {
			return $this->categoryKey();
		}
		elseif($field=='name') {
			return $map['name'];
		}
		elseif($field=='list') {
			return $map['list'];
		}

		return false;
	}

	// Returns the user object
	// (boolean) $field, TRUE returns the value of the field, FALSE returns the object
	public function user($field=false)
	{
		// Get the user object.
		$User = $this->getValue('user');

		if($field) {
			return $User->getField($field);
		}

		return $User;
	}

	// Returns the username who created the post/page
	public function username()
	{
		return $this->getValue('username');
	}

	// Returns the description field
	public function description()
	{
		return $this->getValue('description');
	}

	// Returns the tags
	// (boolean) $returnsArray, TRUE to get the tags as an array, FALSE to get the tags separeted by comma
	public function tags($returnsArray=false)
	{
		$tags = $this->getValue('tags');

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

	public function json($returnsArray=false)
	{
		$tmp['key'] 		= $this->key();
		$tmp['title'] 		= $this->title();
		$tmp['content'] 	= $this->content(); // Markdown parsed
		$tmp['contentRaw'] 	= $this->contentRaw(); // No Markdown parsed
		$tmp['description'] 	= $this->description();
		$tmp['date'] 		= $this->dateRaw();
		$tmp['dateUTC']		= Date::convertToUTC($this->dateRaw(), DB_DATE_FORMAT, DB_DATE_FORMAT);
		$tmp['permalink'] 	= $this->permalink(true);

		if($returnsArray) {
			return $tmp;
		}

		return json_encode($tmp);
	}

	// Returns the file name, FALSE there isn't a cover image setted
	// If the user defined an External Cover Image the complete URL is going to be returned
	// (boolean) $absolute, TRUE returns the absolute path and file name, FALSE just the file name
	public function coverImage($absolute=true)
	{
		$fileName = $this->getValue('coverImage');
		if (empty($fileName)) {
			return false;
		}

		// Check if external cover image, is a valid URL
		if (filter_var($fileName, FILTER_VALIDATE_URL)) {
			return $fileName;
		}

		if ($absolute) {
			return DOMAIN_UPLOADS.$fileName;
		}

		return $fileName;
	}

	// Returns the absolute URL of the thumbnail of the cover image, FALSE if the page doen't have cover image
	public function thumbCoverImage()
	{
		$coverImageFilename = $this->coverImage(false);
		if ($coverImageFilename==false) {
			return false;
		}
		return DOMAIN_UPLOADS_THUMBNAILS.$coverImageFilename;
	}

	// Returns TRUE if the content has the text splited
	public function readMore()
	{
		return $this->getValue('readMore');
	}

	public function uuid()
	{
		return $this->getValue('uuid');
	}

	// Returns the field key
	public function key()
	{
		return $this->getValue('key');
	}

	// (boolean) Returns TRUE if the page is published, FALSE otherwise
	public function published()
	{
		return ($this->getValue('status')==='published');
	}

	// (boolean) Returns TRUE if the page is scheduled, FALSE otherwise
	public function scheduled()
	{
		return ($this->getValue('status')==='scheduled');
	}

	// (boolean) Returns TRUE if the page is draft, FALSE otherwise
	public function draft()
	{
		return ($this->getValue('status')=='draft');
	}

	// (boolean) Returns TRUE if the page is sticky, FALSE otherwise
	public function sticky()
	{
		return ($this->getValue('status')=='sticky');
	}

	// (string) Returns status of the page
	public function status()
	{
		return $this->getValue('status');
	}

	// Returns the title field
	public function title()
	{
		return $this->getValue('title');
	}

	// Returns TRUE if the page has enabled the comments, FALSE otherwise
	public function allowComments()
	{
		return $this->getValue('allowComments');
	}

	// Returns the page position
	public function position()
	{
		return $this->getValue('position');
	}

	// Returns the page slug
	public function slug()
	{
		$explode = explode('/', $this->getValue('key'));

		// Check if the page have a parent.
		if (!empty($explode[1])) {
			return $explode[1];
		}

		return $explode[0];
	}

	// Returns the parent key, if the page doesn't have a parent returns FALSE
	public function parentKey()
	{
		$explode = explode('/', $this->getValue('key'));
		if (isset($explode[1])) {
			return $explode[0];
		}

		return false;
	}

	// Returns the parent method output, if the page doesn't have a parent returns FALSE
	public function parentMethod($method)
	{
		$parentKey = $this->parentKey();
		if ($parentKey) {
			$page = buildPage($parentKey);
			return $page->{$method}();
		}

		return false;
	}

	// Returns TURE if the page has a parent, FALSE otherwise
	public function hasParent()
	{
		return $this->parentKey()!==false;
	}

	// Returns TRUE if the page is a child, FALSE otherwise
	public function isChild()
	{
		return $this->parentKey()!==false;
	}

	// Returns an array with all children's key
	public function children()
	{
		return $this->getValue('children');
	}

	// Returns an array with all children's key
	public function subpages()
	{
		return $this->children();
	}

	// Returns TRUE if the page has children
	public function hasSubpages()
	{
		$subpages = $this->subpages();
		return !empty($subpages);
	}

	// Returns TRUE if the page is a parent
	public function isParent()
	{
		return $this->hasSubpages();
	}


	// Returns relative time (e.g. "1 minute ago")
	// Based on http://stackoverflow.com/a/18602474
	// Modified for Bludit
	// $complete = false : short version
	// $complete = true  : full version
	public function relativeTime($complete = false) {
		$current = new DateTime;
		$past    = new DateTime($this->getValue('date'));
		$elapsed = $current->diff($past);

		$elapsed->w  = floor($elapsed->d / 7);
		$elapsed->d -= $elapsed->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);

		foreach($string as $key => &$value) {
			if($elapsed->$key) {
				$value = $elapsed->$key . ' ' . $value . ($elapsed->$key > 1 ? 's' : ' ');
			} else {
				unset($string[$key]);
			}
		}

		if(!$complete) {
			$string = array_slice($string, 0 , 1);
		}

		return $string ? implode(', ', $string) . ' ago' : 'Just now';

	}
}
