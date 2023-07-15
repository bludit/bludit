<?php defined('BLUDIT') or die('Bludit CMS.');

class Pages extends dbJSON {

	protected $parentKeyList = array();
	protected $dbFields = array(
		'title'=>'',
		'description'=>'',
		'username'=>'',
		'tags'=>array(),
		'type'=>'published', // published, static, draft, sticky, scheduled, autosave
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
			} elseif ($field=='custom') {
				if (isset($args['custom'])) {
					global $site;
					$customFields = $site->customFields();
					foreach ($args['custom'] as $customField=>$customValue) {
						$html = Sanitize::html($customValue);
						// Store the custom field as defined type
						settype($html, $customFields[$customField]['type']);
						$row['custom'][$customField]['value'] = $html;
					}
					unset($args['custom']);
					continue;
				}
			} elseif (isset($args[$field])) {
				// Sanitize if will be stored on database
				$finalValue = Sanitize::html($args[$field]);
			} else {
				// Default value for the field if not defined
				$finalValue = $value;
			}
			// Store the value as defined type
			settype($finalValue, gettype($value));
			$row[$field] = $finalValue;
		}

		// Content
		// This variable is not belong to the database so is not defined in $row
		$contentRaw = (empty($args['content'])?'':$args['content']);

		// Parent
		// This variable is not belong to the database so is not defined in $row
		$parent = '';
		if (!empty($args['parent'])) {
			$parent = $args['parent'];
			$row['type'] = $this->db[$parent]['type']; // get the parent type
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
		if (Filesystem::mkdir(PATH_PAGES.$key, true) === false) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the directory ['.PATH_PAGES.$key.']',LOG_TYPE_ERROR);
			return false;
		}

		// Create the index.txt and save the file
		if (file_put_contents(PATH_PAGES.$key.DS.FILENAME, $contentRaw) === false) {
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

		// Create symlink for images directory
		if (Filesystem::mkdir(PATH_UPLOADS_PAGES.$row['uuid'])) {
			Filesystem::symlink(PATH_UPLOADS_PAGES.$row['uuid'], PATH_UPLOADS_PAGES.$key);
		}

		return $key;
	}

	// Edit a page
	// This function do not edit the current row from the table -
	// - instead of that the function creates a new row and is completed by the current -
	// - values of the page and then the old row is deleted and the new row is inserted.
	public function edit($args)
	{
		// This is the new row for the table and is going to replace the old row
		$row = array();

		// Current key
		// This variable is not belong to the database so is not defined in $row
		$key = $args['key'];

		// Check values from the arguments ($args)
		// If some field is missing the current value is taken
		foreach ($this->dbFields as $field=>$value) {
			if ( ($field=='tags') && isset($args['tags'])) {
				$finalValue = $this->generateTags($args['tags']);
			} elseif ($field=='custom') {
				if (isset($args['custom'])) {
					global $site;
					$customFields = $site->customFields();
					foreach ($args['custom'] as $customField=>$customValue) {
						$html = Sanitize::html($customValue);
						// Store the custom field as defined type
						settype($html, $customFields[$customField]['type']);
						$row['custom'][$customField]['value'] = $html;
					}
					unset($args['custom']);
					continue;
				}
			} elseif (isset($args[$field])) {
				// Sanitize if will be stored on database
				$finalValue = Sanitize::html($args[$field]);
			} else {
				// Default value from the current row
				$finalValue = $this->db[$key][$field];
			}
			settype($finalValue, gettype($value));
			$row[$field] = $finalValue;
		}

		// Parent
		// This variable is not belong to the database so is not defined in $row
		$parent = '';
		if (!empty($args['parent'])) {
			$parent = $args['parent'];
			$row['type'] = $this->db[$parent]['type']; // get the parent type
		}

		// Slug
		// If the user change the slug the page key changes
		// If the user send an empty slug the page key doesn't change
		// This variable is not belong to the database so is not defined in $row
		if (empty($args['slug'])) {
			$explode = explode('/', $key);
			$slug = end($explode);
		} else {
			$slug = $args['slug'];
		}

		// New key
		// The key of the page can change if the user change the slug or the parent, -
		// - if the user doesn't change the slug or the parent the key is going to be the same -
		// - as the current key.
		// This variable is not belong to the database so is not defined in $row
		$newKey = $this->generateKey($slug, $parent, false, $key);

		// if the date in the arguments is not valid, take the value from the old row
		if (!Valid::date($row['date'], DB_DATE_FORMAT)) {
			$row['date'] = $this->db[$key]['date'];
		}

		// Modified date
		$row['dateModified'] = Date::current(DB_DATE_FORMAT);

		// Schedule page
		if (($row['date']>Date::current(DB_DATE_FORMAT)) && ($row['type']=='published')) {
			$row['type'] = 'scheduled';
		}

		// Move the directory from old key to new key only if the keys are different
		if ($newKey!==$key) {
			if (Filesystem::mv(PATH_PAGES.$key, PATH_PAGES.$newKey) === false) {
				Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to move the directory to '.PATH_PAGES.$newKey);
				return false;
			}

			// Regenerate the symlink to a proper directory
			unlink(PATH_UPLOADS_PAGES.$key);
			Filesystem::symlink(PATH_UPLOADS_PAGES.$row['uuid'], PATH_UPLOADS_PAGES.$newKey);
		}

		// If the content was passed via arguments replace the content
		if (isset($args['content'])) {
			// Make the index.txt and save the file.
			if (file_put_contents(PATH_PAGES.$newKey.DS.FILENAME, $args['content'])===false) {
				Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to put the content in the file '.FILENAME);
				return false;
			}
		}

		// Remove the old row
		unset($this->db[$key]);

		// Reindex Orphan Children
		$this->reindexChildren($key, $newKey);

		// Checksum MD5
		$row['md5file'] = md5_file(PATH_PAGES.$newKey.DS.FILENAME);

		// Insert in database the new row
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
			return false;
		}

		// Delete directory and files
		if (Filesystem::deleteRecursive(PATH_PAGES.$key) === false) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the directory '.PATH_PAGES.$key, LOG_TYPE_ERROR);
		}

		// Delete page images directory; The function already check if exists the directory
		if (Filesystem::deleteRecursive(PATH_UPLOADS_PAGES.$key) === false) {
			Log::set(__METHOD__.LOG_SEP.'Directory with images not found '.PATH_UPLOADS_PAGES.$key);
		}

		// Remove from database
		unset($this->db[$key]);

		// Save the database
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

	// Returns a database with all pages
	// $onlyKeys = true; Returns only the pages keys
	// $onlyKeys = false; Returns part of the database, I do not recommend use this
	public function getDB($onlyKeys=true)
	{
		$tmp = $this->db;
		if ($onlyKeys) {
			return array_keys($tmp);
		}
		return $tmp;
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

	// Returns an array with a list of keys/database of autosave pages
	public function getAutosaveDB($onlyKeys=true)
	{
		$tmp = $this->db;
		foreach ($tmp as $key=>$fields) {
			if($fields['type']!='autosave') {
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
	public function getList($pageNumber, $numberOfItems, $published=true, $static=false, $sticky=false, $draft=false, $scheduled=false)
	{
		$list = array();
		foreach ($this->db as $key=>$fields) {
			if ($published && $fields['type']=='published') {
				array_push($list, $key);
			} elseif ($static && $fields['type']=='static') {
				array_push($list, $key);
			} elseif ($sticky && $fields['type']=='sticky') {
				array_push($list, $key);
			} elseif ($draft && $fields['type']=='draft') {
				array_push($list, $key);
			} elseif ($scheduled && $fields['type']=='scheduled') {
				array_push($list, $key);
			}
		}

		if ($numberOfItems==-1) {
			return $list;
		}

		// The first page number is 1, so the real is 0
		$realPageNumber = $pageNumber - 1;

		$total = count($list);
		$init = (int) $numberOfItems * $realPageNumber;
		$end  = (int) min( ($init + $numberOfItems - 1), $total );
		$outrange = $init<0 ? true : $init>$end;
		if (!$outrange) {
			return array_slice($list, $init, $numberOfItems, true);
		}

		return false;
	}

	// Returns the amount of pages
	// (boolean) $onlyPublished, TRUE returns the total of published pages (without draft and scheduled)
	// (boolean) $onlyPublished, FALSE returns the total of pages
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
	// if the UUID doesn't exits returns FALSE
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
			if (isset($this->db[$newKey])) {
				$i = 0;
				while (isset($this->db[$newKey.'-'.$i])) {
					$i++;
				}
				$newKey = $newKey.'-'.$i;
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

	// Insert custom fields to all the pages in the database
	// The structure for the custom fields need to be a valid JSON format
	// The custom fields are incremental, this means the custom fields are never deleted
	// The pages only store the value of the custom field, the structure of the custom fields are in the database site.php
	public function setCustomFields($fields)
	{
		$customFields = json_decode($fields, true);
		if (json_last_error() != JSON_ERROR_NONE) {
			return false;
		}
		foreach ($this->db as $pageKey=>$pageFields) {
			foreach ($customFields as $customField=>$customValues) {
				if (!isset($pageFields['custom'][$customField])) {
					$defaultValue = '';
					if (isset($customValues['default'])) {
						$defaultValue = $customValues['default'];
					}
					$this->db[$pageKey]['custom'][$customField]['value'] = $defaultValue;
				}
			}
		}

		return $this->save();
	}


}
