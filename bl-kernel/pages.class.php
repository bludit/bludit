<?php defined('BLUDIT') or die('Bludit CMS.');

class Pages extends dbJSON {

	protected $parentKeyList = array();
	protected $dbFields = array(
		'title'=>'',
		'description'=>'',
		'username'=>'',
		'tags'=>array(),
		'type'=>'published', // published, draft, sticky, scheduled
		'date'=>'',
		'dateModified'=>'',
		'position'=>0,
		'coverImage'=>'',
		'category'=>'',
		'md5file'=>'',
		'uuid'=>'',
		'allowComments'=>true,
		'template'=>'',
		'noindex'=>false,
		'nofollow'=>false,
		'noarchive'=>false,
		'custom'=>array()
	);

	function __construct()
	{
		parent::__construct(DB_PAGES);
	}

	public function getDefaultFields()
	{
		return $this->dbFields;
	}

	// Return an array with the database for a page, FALSE otherwise
	public function getPageDB($key)
	{
		if ($this->exists($key)) {
			return $this->db[$key];
		}

		return false;
	}

	// Return TRUE if the page exists, FALSE otherwise
	public function exists($key)
	{
		return isset( $this->db[$key] );
	}

	// Create a new page
	// This function returns the key of the new page
	public function add($args)
	{
		$row = array();

		// Predefined values
		foreach ($this->dbFields as $field=>$value) {
			if ($field=='tags') {
				$tags = '';
				if (isset($args['tags'])) {
					$tags = $args['tags'];
				}
				$finalValue = $this->generateTags($tags);
			} elseif (isset($args[$field])) {
				// Sanitize if will be stored on database
				$finalValue = Sanitize::html($args[$field]);
			} else {
				// Default value for the field if not defined
				$finalValue = $value;
			}
			settype($finalValue, gettype($value));
			$row[$field] = $finalValue;
		}

		// Content
		// This variable is not belong to the database so is not defined in $row
		$contentRaw = $args['content'];

		// Parent
		// This variable is not belong to the database so is not defined in $row
		$parent = '';
		if (!empty($args['parent'])) {
			$parent = $args['parent'];
		}

		// Slug from the title or the content
		// This variable is not belong to the database so is not defined in $row
		if (empty($args['slug'])) {
			if (!empty($row['title'])) {
				$slug = $this->generateSlug($row['title']);
			} else {
				$slug = $this->generateSlug($contentRaw);
			}
		} else {
			$slug = $args['slug'];
		}

		// Generate key
		// This variable is not belong to the database so is not defined in $row
		$key = $this->generateKey($slug, $parent);

		// Generate UUID
		if (empty($row['uuid'])) {
			$row['uuid'] = $this->generateUUID();
		}

		// Validate date
		if (!Valid::date($row['date'], DB_DATE_FORMAT)) {
			$row['date'] = Date::current(DB_DATE_FORMAT);
		}

		// Schedule page
		if (($row['date']>Date::current(DB_DATE_FORMAT)) && ($row['type']=='published')) {
			$row['type'] = 'scheduled';
		}

		// Create the directory
		if( Filesystem::mkdir(PATH_PAGES.$key, true) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the directory ['.PATH_PAGES.$key.']',LOG_TYPE_ERROR);
			return false;
		}

		// Create the index.txt and save the file
		if( file_put_contents(PATH_PAGES.$key.DS.FILENAME, $contentRaw) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the content in the file ['.FILENAME.']',LOG_TYPE_ERROR);
			return false;
		}

		// Checksum MD5
		$row['md5file'] = md5_file(PATH_PAGES.$key.DS.FILENAME);

		// Insert in database
		$this->db[$key] = $row;

		// Sort database
		$this->sortBy();

		// Save database
		$this->save();

		return $key;
	}

	public function edit($args)
	{
		// Old key
		// This variable is not belong to the database so is not defined in $row
		$key = $args['key'];

		$row = array();
		// Check values on args or set default values
		foreach ($this->dbFields as $field=>$value) {
			if ($field=='tags') {
				$tags = '';
				if (isset($args['tags'])) {
					$tags = $args['tags'];
				}
				$finalValue = $this->generateTags($tags);
			} elseif (isset($args[$field])) {
				// Sanitize if will be stored on database
				$finalValue = Sanitize::html($args[$field]);
			} else {
				// Default value from the current
				$finalValue = $this->db[$key][$field];
			}
			settype($finalValue, gettype($value));
			$row[$field] = $finalValue;
		}

		// Content
		// This variable is not belong to the database so is not defined in $row
		$contentRaw = $args['content'];

		// Parent
		// This variable is not belong to the database so is not defined in $row
		$parent = '';
		if (!empty($args['parent'])) {
			$parent = $args['parent'];
		}

		// Slug from the title or the content
		// This variable is not belong to the database so is not defined in $row
		if (empty($args['slug'])) {
			if (!empty($row['title'])) {
				$slug = $this->generateSlug($row['title']);
			} else {
				$slug = $this->generateSlug($contentRaw);
			}
		} else {
			$slug = $args['slug'];
		}

		// New key
		// This variable is not belong to the database so is not defined in $row
		$newKey = $this->generateKey($slug, $parent, false, $key);

		// If the page is draft then the created time is the current
		if ($this->db[$key]['type']=='draft') {
			$row['date'] = Date::current(DB_DATE_FORMAT);
		} elseif (!Valid::date($row['date'], DB_DATE_FORMAT)) {
			$row['date'] = $this->db[$key]['date'];
		}

		// Modified date
		$row['dateModified'] = Date::current(DB_DATE_FORMAT);

		// Schedule page
		if (($row['date']>Date::current(DB_DATE_FORMAT)) && ($row['type']=='published')) {
			$row['type'] = 'scheduled';
		}

		// Move the directory from old key to new key.
		if ($newKey!==$key) {
			if( Filesystem::mv(PATH_PAGES.$key, PATH_PAGES.$newKey) === false ) {
				Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to move the directory to '.PATH_PAGES.$newKey);
				return false;
			}
		}

		// Make the index.txt and save the file.
		if (file_put_contents(PATH_PAGES.$newKey.DS.FILENAME, $contentRaw)===false) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to put the content in the file '.FILENAME);
			return false;
		}

		// Remove the old key
		unset( $this->db[$key] );

		// Reindex Orphan Children
		$this->reindexChildren($key, $newKey);

		// Checksum MD5
		$row['md5file'] = md5_file(PATH_PAGES.$newKey.DS.FILENAME);

		// Insert in database
		$this->db[$newKey] = $row;

		// Sort database
		$this->sortBy();

		// Save database
		$this->save();

		return $newKey;
	}

	// This function reindex the orphan children with the new parent key
	// If a page has subpages and the page change his key is necesarry check the children key
	public function reindexChildren($oldParentKey, $newParentKey) {
		if ($oldParentKey==$newParentKey){
			return false;
		}
		$tmp = $this->db;
		foreach ($tmp as $key=>$fields) {
			if (Text::startsWith($key, $oldParentKey.'/')) {
				$newKey = Text::replace($oldParentKey.'/', $newParentKey.'/', $key);
				$this->db[$newKey] = $this->db[$key];
				unset($this->db[$key]);
			}
		}
	}

	public function delete($key)
	{
		// This is need it, because if the key is empty the Filesystem::deleteRecursive is going to delete PATH_PAGES
		if (empty($key)) {
			return false;
		}

		// Page doesn't exist in database
		if (!$this->exists($key)) {
			Log::set(__METHOD__.LOG_SEP.'The page does not exist. Key: '.$key);
		}

		// Delete directory and files
		if (Filesystem::deleteRecursive(PATH_PAGES.$key) === false) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the directory '.PATH_PAGES.$key);
		}

		// Delete page images directory; The function already check if exists the directory
		Filesystem::deleteRecursive(PATH_UPLOADS_PAGES.$this->db[$key]['uuid']);

		// Remove from database
		unset($this->db[$key]);

		// Save the database.
		if ($this->save()===false) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
		}

		return true;
	}

	// Delete all pages from a user
	public function deletePagesByUser($args)
	{
		$username = $args['username'];

		foreach ($this->db as $key=>$fields) {
			if ($fields['username']===$username) {
				$this->delete($key);
			}
		}

		return true;
	}

	// Link all pages to a new user
	public function transferPages($args)
	{
		$oldUsername = $args['oldUsername'];
		$newUsername = isset($args['newUsername']) ? $args['newUsername'] : 'admin';

		foreach ($this->db as $key=>$fields) {
			if ($fields['username']===$oldUsername) {
				$this->db[$key]['username'] = $newUsername;
			}
		}

		return $this->save();
	}

	// Set field = value
	public function setField($key, $field, $value)
	{
		if ($this->exists($key)) {
			settype($value, gettype($this->dbFields[$field]));
			$this->db[$key][$field] = $value;
			return $this->save();
		}
		return false;
	}

	// Returns a database with published pages
	// $onlyKeys = true; Returns only the pages keys
	// $onlyKeys = false; Returns part of the database, I do not recommend use this
	public function getPublishedDB($onlyKeys=true)
	{
		$tmp = $this->db;
		foreach ($tmp as $key=>$fields) {
			if ($fields['type']!='published') {
				unset($tmp[$key]);
			}
		}
		if ($onlyKeys) {
			return array_keys($tmp);
		}
		return $tmp;
	}

	// Returns an array with a list of keys/database of static pages
	// By default the static pages are sort by position
	public function getStaticDB($onlyKeys=true)
	{
		$tmp = $this->db;
		foreach ($tmp as $key=>$fields) {
			if ($fields['type']!='static') {
				unset($tmp[$key]);
			}
		}
		uasort($tmp, array($this, 'sortByPositionLowToHigh'));
		if ($onlyKeys) {
			return array_keys($tmp);
		}
		return $tmp;
	}

	// Returns an array with a list of keys/database of draft pages
	public function getDraftDB($onlyKeys=true)
	{
		$tmp = $this->db;
		foreach ($tmp as $key=>$fields) {
			if($fields['type']!='draft') {
				unset($tmp[$key]);
			}
		}
		if ($onlyKeys) {
			return array_keys($tmp);
		}
		return $tmp;
	}

	// Returns an array with a list of keys/database of scheduled pages
	public function getScheduledDB($onlyKeys=true)
	{
		$tmp = $this->db;
		foreach ($tmp as $key=>$fields) {
			if($fields['type']!='scheduled') {
				unset($tmp[$key]);
			}
		}
		if ($onlyKeys) {
			return array_keys($tmp);
		}
		return $tmp;
	}

	// Returns an array with a list of keys of sticky pages
	public function getStickyDB($onlyKeys=true)
	{
		$tmp = $this->db;
		foreach ($tmp as $key=>$fields) {
			if($fields['type']!='sticky') {
				unset($tmp[$key]);
			}
		}
		if ($onlyKeys) {
			return array_keys($tmp);
		}
		return $tmp;
	}

	// Returns the next number of the bigger position
	public function nextPositionNumber()
	{
		$tmp = 1;
		foreach ($this->db as $key=>$fields) {
			if ($fields['position']>$tmp) {
				$tmp = $fields['position'];
			}
		}
		return ++$tmp;
	}

	// Returns the next page key of the current page key
	public function nextPageKey($currentKey)
	{
		if ($this->db[$currentKey]['type']=='published') {
			$keys = array_keys($this->db);
			$position = array_search($currentKey, $keys) - 1;
			if (isset($keys[$position])) {
				$nextKey = $keys[$position];
				if ($this->db[$nextKey]['type']=='published') {
					return $nextKey;
				}
			}
		}
		return false;
	}

	// Returns the previous page key of the current page key
	public function previousPageKey($currentKey)
	{
		if ($this->db[$currentKey]['type']=='published') {
			$keys = array_keys($this->db);
			$position = array_search($currentKey, $keys) + 1;
			if (isset($keys[$position])) {
				$prevKey = $keys[$position];
				if ($this->db[$prevKey]['type']=='published') {
					return $prevKey;
				}
			}
		}
		return false;
	}

	// Returns an array with a list of key of pages, FALSE if out of range
	// The database is sorted by date or by position
	// (int) $pageNumber, the page number
	// (int) $numberOfItems, amount of items to return, if -1 returns all the items
	// (boolean) $onlyPublished, TRUE to return only published pages
	public function getList($pageNumber, $numberOfItems, $onlyPublished=true)
	{
		$db = array_keys($this->db);

		if ($onlyPublished) {
			$db = $this->getPublishedDB(true);
		}

		if ($numberOfItems==-1) {
			return $db;
		}

		// The first page number is 1, so the real is 0
		$realPageNumber = $pageNumber - 1;

		$total = count($db);
		$init = (int) $numberOfItems * $realPageNumber;
		$end  = (int) min( ($init + $numberOfItems - 1), $total );
		$outrange = $init<0 ? true : $init>$end;

		if (!$outrange) {
			return array_slice($db, $init, $numberOfItems, true);
		}

		return false;
	}



	// Returns the amount of pages
	// (boolean) $total, TRUE returns the total of pages
	// (boolean) $total, FALSE returns the total of published pages (without draft and scheduled)
	public function count($onlyPublished=true)
	{
		if ($onlyPublished) {
			$db = $this->getPublishedDB(false);
			return count($db);
		}

		return count($this->db);
	}

	// Returns an array with all parents pages key. A parent page is not a child
	public function getParents()
	{
		$db = $this->getPublishedDB();
		foreach ($db as $key=>$pageKey) {
			// if the key has slash then is a child
			if (Text::stringContains($pageKey, '/')) {
				unset($db[$key]);
			}
		}
		return $db;
	}

	public function getChildren($parentKey)
	{
		$tmp = $this->db;
		$list = array();
		foreach ($tmp as $key=>$fields) {
			if (Text::startsWith($key, $parentKey.'/')) {
				array_push($list, $key);
			}
		}
		return $list;
	}



	public function sortBy()
	{
		if (ORDER_BY=='date') {
			return $this->sortByDate(true);
		}
		return $this->sortByPosition(false);
	}

	// Sort pages by position
	public function sortByPosition($HighToLow=false)
	{
		if($HighToLow) {
			uasort($this->db, array($this, 'sortByPositionHighToLow'));
		} else {
			uasort($this->db, array($this, 'sortByPositionLowToHigh'));
		}
		return true;
	}

	private function sortByPositionLowToHigh($a, $b)
	{
		return $a['position']>$b['position'];
	}
	private function sortByPositionHighToLow($a, $b)
	{
		return $a['position']<$b['position'];
	}

	// Sort pages by date
	public function sortByDate($HighToLow=true)
	{
		if($HighToLow) {
			uasort($this->db, array($this, 'sortByDateHighToLow'));
		} else {
			uasort($this->db, array($this, 'sortByDateLowToHigh'));
		}
		return true;
	}

	private function sortByDateLowToHigh($a, $b)
	{
		return $a['date']>$b['date'];
	}
	private function sortByDateHighToLow($a, $b)
	{
		return $a['date']<$b['date'];
	}

	function generateUUID() {
		return md5( uniqid().time() );
	}

	// Returns the UUID of a page, by the page key
	function getUUID($key)
	{
		if ($this->exists($key)) {
			return $this->db[$key]['uuid'];
		}
		return false;
	}

	// Returns the page key by the uuid
	function getByUUID($uuid)
	{
		foreach ($this->db as $key=>$value) {
			if ($value['uuid']==$uuid) {
				return $key;
			}
		}
		return false;
	}


	// Returns string without HTML tags and truncated
	private function generateSlug($text, $truncateLength=60)
	{
		$tmpslug = Text::removeHTMLTags($text);
		$tmpslug = Text::removeLineBreaks($tmpslug);
		$tmpslug = Text::truncate($tmpslug, $truncateLength, '');
		return $tmpslug;
	}

	// Returns TRUE if there are new pages published, FALSE otherwise
	public function scheduler()
	{
		// Get current date
		$currentDate = Date::current(DB_DATE_FORMAT);
		$saveDatabase = false;

		// The database need to be sorted by date
		foreach($this->db as $pageKey=>$fields) {
			if($fields['type']=='scheduled') {
				if($fields['date']<=$currentDate) {
					$this->db[$pageKey]['type'] = 'published';
					$saveDatabase = true;
				}
			}
			elseif( ($fields['type']=='published') && (ORDER_BY=='date') ) {
				break;
			}
		}

		if($saveDatabase) {
			if( $this->save() === false ) {
				Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
				return false;
			}

			Log::set(__METHOD__.LOG_SEP.'New pages published from the scheduler.');
			return true;
		}

		return false;
	}

	// Generate a valid Key/Slug
	public function generateKey($text, $parent=false, $returnSlug=false, $oldKey='')
	{
		global $L;
		global $site;

		if (Text::isEmpty($text)) {
			$text = $L->g('empty');
		}

		if (Text::isEmpty($parent)) {
			$newKey = Text::cleanUrl($text);
		} else {
			$newKey = Text::cleanUrl($parent).'/'.Text::cleanUrl($text);
		}

		// cleanURL can return empty string
		if (Text::isEmpty($newKey)) {
			$newKey = $L->g('empty');
		}

		if ($newKey!==$oldKey) {
			// Verify if the key is already been used
			if( isset($this->db[$newKey]) ) {
				if( !Text::endsWithNumeric($newKey) ) {
					$newKey = $newKey.'-0';
				}

				while( isset($this->db[$newKey]) ) {
					$newKey++;
				}
			}
		}

		if ($returnSlug) {
			$explode = explode('/', $newKey);
			if (isset($explode[1])) {
				return $explode[1];
			}
			return $explode[0];
		}

		return $newKey;
	}

	// Returns an Array, array('tagSlug'=>'tagName')
	// (string) $tags, tag list separated by comma.
	public function generateTags($tags)
	{
		$tmp = array();
		$tags = trim($tags);
		if (empty($tags)) {
			return $tmp;
		}

		$tags = explode(',', $tags);
		foreach ($tags as $tag) {
			$tag = trim($tag);
			$tagKey = Text::cleanUrl($tag);
			$tmp[$tagKey] = $tag;
		}
		return $tmp;
	}

	// Change all pages with the old category key to the new category key
	public function changeCategory($oldCategoryKey, $newCategoryKey)
	{
		foreach ($this->db as $key=>$value) {
			if ($value['category']===$oldCategoryKey) {
				$this->db[$key]['category'] = $newCategoryKey;
			}
		}
		return $this->save();
	}

}
