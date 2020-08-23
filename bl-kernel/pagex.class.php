<?php defined('BLUDIT') or die('Bludit CMS.');

class Page {

	protected $vars;

	function __construct($key)
	{
		global $pages;

		$this->vars['key'] = $key;

		// If key is FALSE, the page is create with default values, like an empty page
		// Useful for Page Not Found
		if ($key===false) {
			$row = $pages->getDefaultFields();
		} else {
			if (Text::isEmpty($key) || !$pages->exists($key)) {
				$errorMessage = 'Page not found in database by key ['.$key.']';
				Log::set(__METHOD__.LOG_SEP.$errorMessage);
				throw new Exception($errorMessage);
			}
			$row = $pages->getPageDB($key);
		}

		foreach ($row as $field=>$value) {
			if ($field=='date') {
				$this->setField('dateRaw', $value);
			} else {
				$this->setField($field, $value);
			}
		}
	}

	public function getValue($field)
	{
		if (isset($this->vars[$field])) {
			return $this->vars[$field];
		}
		return false;
	}

	public function setField($field, $value)
	{
		$this->vars[$field] = $value;
		return true;
	}

	// Returns the raw content
	// This content is not markdown parser
	// (boolean) $sanitize, TRUE returns the content sanitized
	public function contentRaw($sanitize=false)
	{
		$key = $this->key();
		$filePath = PATH_PAGES.$key.DS.FILENAME;
		$contentRaw = file_get_contents($filePath);

		if ($sanitize) {
			return Sanitize::html($contentRaw);
		}
		return $contentRaw;
	}

	// Returns the full content
	// This content is markdown parser
	// (boolean) $sanitize, TRUE returns the content sanitized
	public function content($sanitize=false)
	{
		// If already set the content, return it
		$content = $this->getValue('content');
		if (!empty($content)) {
			return $content;
		}

		// Get the raw content
		$content = $this->contentRaw();

		// Parse Markdown
		if (MARKDOWN_PARSER) {
			$parsedown = new Parsedown();
			$content = $parsedown->text($content);
		}

		// Parse img src relative to absolute (with domain)
		if (IMAGE_RELATIVE_TO_ABSOLUTE) {
			$domain = IMAGE_RESTRICT?DOMAIN_UPLOADS_PAGES.$this->uuid().'/':DOMAIN_UPLOADS;
			$content = Text::imgRel2Abs($content, $domain);
		}

		if ($sanitize) {
			return Sanitize::html($content);
		}
		return $content;
	}

	// Returns the first part of the content if the content is splited, otherwise is returned the full content
	// This content is markdown parser
	// (boolean) $sanitize, TRUE returns the content sanitized
	public function contentBreak($sanitize=false)
	{
		$content = $this->content($sanitize);
		$explode = explode(PAGE_BREAK, $content);
		return $explode[0];
	}

	// Returns the date according to locale settings and the format defined in the system
	public function date($format=false)
	{
		$dateRaw = $this->dateRaw();
		if ($format===false) {
			global $site;
			$format = $site->dateFormat();
		}
		return Date::format($dateRaw, DB_DATE_FORMAT, $format);
	}

	// Returns the date according to locale settings and format as database stored
	public function dateRaw()
	{
		// This field is set in the constructor
		return $this->getValue('dateRaw');
	}

	// Returns the date according to locale settings and format settings
	public function dateModified($format=false)
	{
		$dateRaw = $this->getValue('dateModified');
		if ($format===false) {
			global $site;
			$format = $site->dateFormat();
		}
		return Date::format($dateRaw, DB_DATE_FORMAT, $format);
	}

	// Returns the username who created the page
	public function username()
	{
		return $this->getValue('username');
	}

	// TODO: Check if necessary this function
	public function getDB()
	{
		return $this->vars;
	}

	// Returns the permalink
	// (boolean) $absolute, TRUE returns the page link with the DOMAIN, FALSE without the DOMAIN
	public function permalink($absolute=true)
	{
		// Get the key of the page
		$key = $this->key();

		if($absolute) {
			return DOMAIN_PAGES.$key;
		}

		return HTML_PATH_ROOT.PAGE_URI_FILTER.$key;
	}

	// Returns the previous page key
	public function previousKey()
	{
		global $pages;
		return $pages->previousPageKey($this->key());
	}

	// Returns the next page key
	public function nextKey()
	{
		global $pages;
		return $pages->nextPageKey($this->key());
	}

	// Returns the category name
	public function category()
	{
		return $this->categoryMap('name');
	}

	// Returns the category template
	public function categoryTemplate()
	{
		return $this->categoryMap('template');
	}

	// Returns the category description
	public function categoryDescription()
	{
		return $this->categoryMap('description');
	}

	// Returns the category key
	public function categoryKey()
	{
		return $this->getValue('category');
	}

	// Returns the category permalink
	public function categoryPermalink()
	{
		return DOMAIN_CATEGORIES.$this->categoryKey();
	}

	// Returns the field from the array
	// categoryMap = array( 'name'=>'', 'list'=>array() )
	public function categoryMap($field)
	{
		global $categories;
		$categoryKey = $this->categoryKey();
		$map = $categories->getMap($categoryKey);

		if ($field=='key') {
			return $this->categoryKey();
		} elseif(isset($map[$field])) {
			return $map[$field];
		}

		return false;
	}

	// Returns the user object or passing the method returns the object User method
	public function user($method=false)
	{
		$username = $this->username();
		try {
			$user = new User($username);
			if ($method) {
				return $user->{$method}();
			}
			return $user;
		} catch (Exception $e) {
			return false;
		}
	}

	public function template()
	{
		return $this->getValue('template');
	}

	// Returns the description field
	public function description()
	{
		return $this->getValue('description');
	}

	// Returns the tags separated by comma
	// (boolean) $returnsArray, TRUE to get the tags as an array, FALSE to get the tags separated by comma
	// The tags in array format returns array( tagKey => tagName )
	public function tags($returnsArray=false)
	{
		$tags = $this->getValue('tags');
		if ($returnsArray) {
			if (empty($tags)) {
				return array();
			}
			return $tags;
		}

		if (empty($tags)) {
			return '';
		}
		// Return string with tags separated by comma.
		return implode(',', $tags);
	}



	public function json($returnsArray=false)
	{
		$tmp['key'] 		= $this->key();
		$tmp['title'] 		= $this->title();
		$tmp['content'] 	= $this->content(); // Markdown parsed
		$tmp['contentRaw'] 	= $this->contentRaw(true); // No Markdown parsed
		$tmp['description'] 	= $this->description();
		$tmp['type'] 		= $this->type();
		$tmp['slug'] 		= $this->slug();
		$tmp['date'] 		= $this->date();
		$tmp['dateRaw'] 	= $this->dateRaw();
		$tmp['tags'] 		= $this->tags(false);
		$tmp['username'] 	= $this->username();
		$tmp['category'] 	= $this->category();
		$tmp['uuid'] 		= $this->uuid();
		$tmp['dateUTC']		= Date::convertToUTC($this->dateRaw(), DB_DATE_FORMAT, DB_DATE_FORMAT);
		$tmp['permalink'] 	= $this->permalink(true);
		$tmp['coverImage'] 		= $this->coverImage(true);
		$tmp['coverImageFilename'] 	= $this->coverImage(false);

		if ($returnsArray) {
			return $tmp;
		}

		return json_encode($tmp);
	}

	// Returns the endpoint of the coverimage, FALSE if the page doesn't have a cover image
	// (boolean) $absolute, TRUE returns the complete URL, FALSE returns the filename
	// If the user defined an external cover image the function returns it
	public function coverImage($absolute=true)
	{
		$filename = $this->getValue('coverImage');
		if (empty($filename)) {
			return false;
		}

		// Check is external cover image
		if (filter_var($filename, FILTER_VALIDATE_URL)) {
			return $filename;
		}

		if ($absolute) {
			if (IMAGE_RESTRICT) {
				return DOMAIN_UPLOADS_PAGES.$this->uuid().'/'.$filename;
			}
			return DOMAIN_UPLOADS.$filename;
		}

		return $filename;
	}

	// Returns the endpoint of the thumbnail cover image, FALSE if the page doesn't have a cover image
	public function thumbCoverImage()
	{
		$filename = $this->coverImage(false);
		if ($filename==false) {
			return false;
		}

		// Check is external cover image
		if (filter_var($filename, FILTER_VALIDATE_URL)) {
			return $filename;
		}

		if (IMAGE_RESTRICT) {
			return DOMAIN_UPLOADS_PAGES.$this->uuid().'/thumbnails/'.$filename;
		}
		return DOMAIN_UPLOADS_THUMBNAILS.$filename;
	}

	// Returns TRUE if the content has the text splited
	public function readMore()
	{
		$content = $this->contentRaw();
		return Text::stringContains($content, PAGE_BREAK);
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
		return ($this->getValue('type')==='published');
	}

	// (boolean) Returns TRUE if the page is scheduled, FALSE otherwise
	public function scheduled()
	{
		return ($this->getValue('type')==='scheduled');
	}

	// (boolean) Returns TRUE if the page is draft, FALSE otherwise
	public function draft()
	{
		return ($this->getValue('type')=='draft');
	}

	// (boolean) Returns TRUE if the page is autosave, FALSE otherwise
	public function autosave()
	{
		return ($this->getValue('type')=='autosave');
	}

	// (boolean) Returns TRUE if the page is sticky, FALSE otherwise
	public function sticky()
	{
		return ($this->getValue('type')=='sticky');
	}

	// (boolean) Returns TRUE if the page is static, FALSE otherwise
	public function isStatic()
	{
		return ($this->getValue('type')=='static');
	}

	// (string) Returns type of the page
	public function type()
	{
		return $this->getValue('type');
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

	// Returns the page noindex
	public function noindex()
	{
		return $this->getValue('noindex');
	}

	// Returns the page nofollow
	public function nofollow()
	{
		return $this->getValue('nofollow');
	}

	// Returns the page noarchive
	public function noarchive()
	{
		return $this->getValue('noarchive');
	}

	// Returns the page slug
	public function slug()
	{
		$explode = explode('/', $this->key());
		return end($explode);
	}

	// Returns the parent key, if the page doesn't have a parent returns FALSE
	public function parent()
	{
		return $this->parentKey();
	}

	// Returns the parent key, if the page doesn't have a parent returns FALSE
	public function parentKey()
	{
		$explode = explode('/', $this->key());
		if (isset($explode[1])) {
			return $explode[0];
		}
		return false;
	}

	// Returns TRUE if the page is a parent, has or not children
	public function isParent()
	{
		return $this->parentKey()===false;
	}

	// Returns the parent method output, if the page doesn't have a parent returns FALSE
	public function parentMethod($method)
	{
		$parentKey = $this->parentKey();
		if ($parentKey) {
			try {
				$page = new Page($parentKey);
				return $page->{$method}();
			} catch (Exception $e) {
				// Continoue
			}
		}

		return false;
	}

	// Returns TRUE if the page is a child, FALSE otherwise
	public function isChild()
	{
		return $this->parentKey()!==false;
	}

	// Returns TRUE if the page has children
	public function hasChildren()
	{
		$childrenKeys = $this->childrenKeys();
		return !empty($childrenKeys);
	}

	// Returns an array with all children's keys
	public function childrenKeys()
	{
		global $pages;
		$key = $this->key();
		return $pages->getChildren($key);
	}

	// Returns an array with all children as Page-Object
	public function children()
	{
		global $pages;
		$list = array();
		$childrenKeys = $pages->getChildren($this->key());
		foreach ($childrenKeys as $childKey) {
			try {
				$child = new Page($childKey);
				array_push($list, $child);
			} catch (Exception $e) {
				// Continue
			}
		}

		return $list;
	}

	// Returns the amount of minutes takes to read the page
	public function readingTime() {
		global $L;

		$words = $this->content(true);
		$words = strip_tags($words);
		$words = str_word_count($words);
		$average = $words / 200;
		$minutes = round($average);

		if ($minutes>1) {
			return $minutes.' '.$L->get('minutes');
		}

		return '~1 '.$L->get('minute');
	}

	// Returns relative time (e.g. "1 minute ago")
	// Based on http://stackoverflow.com/a/18602474
	// Modified for Bludit
	// $complete = false : short version
	// $complete = true  : full version
	public function relativeTime($complete = false) {
		$current = new DateTime;
		$past    = new DateTime($this->getValue('dateRaw'));
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

	// Returns the value from the field, false if the fields doesn't exists
	// If you set the $option as TRUE, the function returns an array with all the values of the field
	public function custom($field, $options=false)
	{
		if (isset($this->vars['custom'][$field])) {
			if ($options) {
				return $this->vars['custom'][$field];
			}
			return $this->vars['custom'][$field]['value'];
		}
		return false;
	}

	// Returns an array with all pages key related to the page
	// The relation is based on the tags
	public function related() {
		global $tags;
		$pageTags = $this->tags(true);
		$list = array();
		// For each tag get the list of related pages
		foreach ($pageTags as $tagKey=>$tagName) {
			$pagesRelated = $tags->getList($tagKey, 1, -1);
			$list = array_merge($list, $pagesRelated);
		}

		// Remove duplicates
		$list = array_unique($list);

		// Remove himself from the list
		if (($key = array_search($this->key(), $list)) !== false) {
			unset($list[$key]);
		}

		return $list;
	}
}
