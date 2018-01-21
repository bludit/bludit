<?php defined('BLUDIT') or die('Bludit CMS.');

class dbPages extends dbJSON
{
	private $parentKeyList = array();

	private $dbFields = array(
		'title'=>		array('inFile'=>true,	'value'=>''),
		'content'=>		array('inFile'=>true,	'value'=>''),
		'description'=>		array('inFile'=>false,	'value'=>''),
		'username'=>		array('inFile'=>false,	'value'=>''),
		'tags'=>		array('inFile'=>false,	'value'=>array()),
		'status'=>		array('inFile'=>false,	'value'=>'published'), // published, draft, scheduled
		'type'=>		array('inFile'=>false,	'value'=>'post'), // post, page
		'date'=>		array('inFile'=>false,	'value'=>''),
		'dateModified'=>	array('inFile'=>false,	'value'=>''),
		'position'=>		array('inFile'=>false,	'value'=>0),
		'coverImage'=>		array('inFile'=>false,	'value'=>''),
		'category'=>		array('inFile'=>false,	'value'=>''),
		'md5file'=>		array('inFile'=>false,	'value'=>''),
		'uuid'=>		array('inFile'=>false,	'value'=>''),
		'allowComments'=>	array('inFile'=>false,	'value'=>true),
		'template'=>		array('inFile'=>false,	'value'=>'')
	);

	function __construct()
	{
		parent::__construct(DB_PAGES);
	}

	// Create a new page
	// This function returns the key of the new page
	public function add($args, $climode=false)
	{
		$dataForDb = array();	// This data will be saved in the database
		$dataForFile = array(); // This data will be saved in the file

		// Check values on args or set default values
		foreach ($this->dbFields as $field=>$options) {
			if (isset($args[$field])) {
				if ($options['inFile'] || is_array($args[$field])) {
					$value = $args[$field];
				} else {
					// Sanitize if will be stored on database
					$value = Sanitize::html($args[$field]);
				}
			} else {
				// Default value for the field
				$value = $options['value'];
			}

			$args[$field] = $value;
		}

		// Tags
		if (!empty($args['tags'])) {
			$args['tags'] = $this->generateTags($args['tags']);
		} else {
			$args['tags'] = array();
		}

		// Slug from title or content
		if (empty($args['slug'])) {
			if (!empty($args['title'])) {
				$args['slug'] = $this->generateSlug($args['title']);
			} else {
				$args['slug'] = $this->generateSlug($args['content']);
			}
		}

		// Parent
		if (!isset($args['parent'])) {
			$args['parent'] = '';
		}

		// Generate key
		$key = $this->generateKey($args['slug'], $args['parent']);

		// Generate UUID
		$args['uuid'] = $this->generateUUID();

		// Validate date
		if (!Valid::date($args['date'], DB_DATE_FORMAT)) {
			$args['date'] = Date::current(DB_DATE_FORMAT);
		}

		// Schedule page
		if (($args['date']>Date::current(DB_DATE_FORMAT)) && ($args['status']=='published')) {
			$args['status'] = 'scheduled';
		}

		// Set type of the page
		if ($args['status']=='static') {
			$args['type'] = 'page';
		}

		// Set type to the variables
		foreach ($this->dbFields as $field=>$options) {
			$value = $args[$field];

			if ($options['inFile']) {
				// Save on file
				$dataForFile[$field] = $this->stylingFieldsForFile($field, $value);
			} else {
				// Set type
				settype($value, gettype($options['value']));

				// Save on database
				$dataForDb[$field] = $value;
			}
		}

		if ($climode===false) {
			// Create the directory
			if( Filesystem::mkdir(PATH_PAGES.$key, true) === false ) {
				Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the directory '.PATH_PAGES.$key);
				return false;
			}

			// Make the index.txt and save the file.
			$data = implode(PHP_EOL, $dataForFile);
			if( file_put_contents(PATH_PAGES.$key.DS.FILENAME, $data) === false ) {
				Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to put the content in the file '.FILENAME);
				return false;
			}
		}

		// Checksum MD5
		$dataForDb['md5file'] = md5_file(PATH_PAGES.$key.DS.FILENAME);

		// Insert in database
		$this->db[$key] = $dataForDb;

		// Sort database
		$this->sortBy();

		// Save database
		$this->save();

		return $key;
	}

	public function edit($args, $climode=false)
	{
		$dataForDb = array();
		$dataForFile = array();

		// Check values on args or set default values
		foreach ($this->dbFields as $field=>$options) {
			if (isset($args[$field])) {
				if ($options['inFile'] || is_array($args[$field])) {
					$value = $args[$field];
				} else {
					// Sanitize if will be stored on database
					$value = Sanitize::html($args[$field]);
				}
			} else {
				// By default is the current value
				if (isset($this->db[$args['key']][$field])) {
					$value = $this->db[$args['key']][$field];
				} else {
					$value = $options['value'];
				}
			}

			$args[$field] = $value;
		}

		// Tags
		if (!empty($args['tags'])) {
			$args['tags'] = $this->generateTags($args['tags']);
		} else {
			$args['tags'] = array();
		}

		// Parent
		if (!isset($args['parent'])) {
			$args['parent'] = '';
		}

		$newKey = $this->generateKey($args['slug'], $args['parent'], false, $args['key']);

		// If the page is draft then the created time is the current
		if ($this->db[$args['key']]['status']=='draft') {
			$args['date'] = Date::current(DB_DATE_FORMAT);
		} elseif (!Valid::date($args['date'], DB_DATE_FORMAT)) {
			$args['date'] = $this->db[$args['key']]['date'];
		}

		// Modified date
		$args['dateModified'] = Date::current(DB_DATE_FORMAT);

		// Schedule page
		if (($args['date']>Date::current(DB_DATE_FORMAT)) && ($args['status']=='published')) {
			$args['status'] = 'scheduled';
		}

		// Set type of the page
		if ($args['status']=='static') {
			$args['type'] = 'page';
		}

		// Set type to the variables
		foreach ($this->dbFields as $field=>$options) {
			$value = $args[$field];

			if ($options['inFile']) {
				// Save on file
				$dataForFile[$field] = $this->stylingFieldsForFile($field, $value);
			} else {
				// Set type
				settype($value, gettype($options['value']));

				// Save on database
				$dataForDb[$field] = $value;
			}
		}

		if ($climode===false) {
			// Move the directory from old key to new key.
			if ($newKey!==$args['key']) {
				if( Filesystem::mv(PATH_PAGES.$args['key'], PATH_PAGES.$newKey) === false ) {
					Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to move the directory to '.PATH_PAGES.$newKey);
					return false;
				}
			}

			// Make the index.txt and save the file.
			$data = implode("\n", $dataForFile);
			if (file_put_contents(PATH_PAGES.$newKey.DS.FILENAME, $data)===false) {
				Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to put the content in the file '.FILENAME);
				return false;
			}
		}

		// Remove the old key
		unset( $this->db[$args['key']] );

		// Reindex Orphan Children
		$this->reindexChildren($args['key'], $newKey);

		// Checksum MD5
		$dataForDb['md5file'] = md5_file(PATH_PAGES.$newKey.DS.FILENAME);

		// Insert in database
		$this->db[$newKey] = $dataForDb;

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

		// Remove from database
		unset($this->db[$key]);

		// Save the database.
		if ($this->save()===false) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
		}

		return true;
	}

	// Change a field's value
	public function setField($key, $field, $value)
	{
		if ($this->exists($key)) {
			settype($value, gettype($this->dbFields[$field]['value']));
			$this->db[$key][$field] = $value;
			return $this->save();
		}

		return false;
	}

	// Returns a database with published pages keys
	public function getPublishedDB($onlyKeys=true)
	{
		$tmp = $this->db;
		foreach ($tmp as $key=>$fields) {
			if ($fields['status']!='published') {
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
			if ($fields['status']!='static') {
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
			if($fields['status']!='draft') {
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
			if($fields['status']!='scheduled') {
				unset($tmp[$key]);
			}
		}
		if ($onlyKeys) {
			return array_keys($tmp);
		}
		return $tmp;
	}

	// Return an array with the database for a page, FALSE otherwise
	public function getPageDB($key)
	{
		if ($this->exists($key)) {
			return $this->db[$key];
		}

		return false;
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

	// Returns an array with a list of key of pages, FALSE if out of range
	// The database is sorted by date or by position
	// (int) $pageNumber, the page number
	// (int) $amountOfItems, amount of items to return, if -1 returns all the items
	// (boolean) $onlyPublished, TRUE to return only published pages
	public function getList($pageNumber, $amountOfItems, $onlyPublished=true)
	{
		$db = array_keys($this->db);

		if ($onlyPublished) {
			$db = $this->getPublishedDB(true);
		}

		if ($amountOfItems==-1) {
			return $db;
		}

		// The first page number is 1, so the real is 0
		$realPageNumber = $pageNumber - 1;

		$total = count($db);
		$init = (int) $amountOfItems * $realPageNumber;
		$end  = (int) min( ($init + $amountOfItems - 1), $total );
		$outrange = $init<0 ? true : $init>$end;

		if (!$outrange) {
			return array_slice($db, $init, $amountOfItems, true);
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

	// Return TRUE if the page exists, FALSE otherwise
	public function exists($key)
	{
		return isset( $this->db[$key] );
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

	private function generateUUID() {
		return md5( uniqid().time() );
	}

	// Returns string without HTML tags and truncated
	private function generateSlug($text, $truncateLength=60) {
		$tmpslug = Text::removeHTMLTags($text);
		return Text::truncate($tmpslug, $truncateLength, '');
	}

	// Returns TRUE if there are new pages published, FALSE otherwise
	public function scheduler()
	{
		// Get current date
		$currentDate = Date::current(DB_DATE_FORMAT);
		$saveDatabase = false;

		// The database need to be sorted by date
		foreach($this->db as $pageKey=>$fields) {
			if($fields['status']=='scheduled') {
				if($fields['date']<=$currentDate) {
					$this->db[$pageKey]['status'] = 'published';
					$saveDatabase = true;
				}
			}
			elseif( ($fields['status']=='published') && (ORDER_BY=='date') ) {
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
		if (Text::isEmpty($text)) {
			$text = 'empty';
		}

		if (Text::isEmpty($parent)) {
			$newKey = Text::cleanUrl($text);
		} else {
			$newKey = Text::cleanUrl($parent).'/'.Text::cleanUrl($text);
		}

		// cleanURL can return empty string
		if (Text::isEmpty($newKey)) {
			$newKey = 'empty';
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

			if(isset($explode[1])) {
				return $explode[1];
			}

			return $explode[0];
		}

		return $newKey;
	}

	public function rescanClimode()
	{
		Log::set('CLI MODE'.LOG_SEP.'Starting re-scan on pages directory.');
		$pageList = array();

		// Search for pages
		$directories = Filesystem::listDirectories(PATH_PAGES, $regex='*', $sortByDate=false);
		foreach($directories as $directory) {
			if( Sanitize::pathFile($directory.DS.FILENAME) ) {
				$pageKey = basename($directory);
				$pageList[$pageKey] = true;

				// Search for children pages
				$subDirectories = Filesystem::listDirectories(PATH_PAGES.$pageKey.DS, $regex='*', $sortByDate=false);
				foreach($subDirectories as $subDirectory) {
					if( Sanitize::pathFile($subDirectory.DS.FILENAME) ) {
						$subPageKey = basename($subDirectory);
						$subPageKey = $pageKey.'/'.$subPageKey;
						$pageList[$subPageKey] = true;
					}
				}
			}
		}

		Log::set('CLI MODE'.LOG_SEP.'Updating pages...');
		$keys = array_keys($pageList);
		foreach($keys as $pageKey) {
			// Checksum
			$checksum = md5_file(PATH_PAGES.$pageKey.DS.FILENAME);

			// New page
			if( !isset($this->db[$pageKey]) ) {
				$this->verifyFieldsClimode($pageKey, true);
			}
			// Update page
			elseif($this->db[$pageKey]['md5file']!=$checksum) {
				$this->verifyFieldsClimode($pageKey, false);
			}
		}

		Log::set('CLI MODE'.LOG_SEP.'Removing pages...');
		foreach( array_diff_key($this->db, $pageList) as $pageKey=>$data ) {
			Log::set('CLI MODE'.LOG_SEP.'Removing page from database, key: '.$pageKey);
			unset( $this->db[$pageKey] );
		}
		$this->save();
	}

	private function verifyFieldsClimode($key, $insert=true)
	{
		$page = new Page($key);
		$db = $page->getDB();

		// Content from file
		$db['content'] = $db['contentRaw'];

		// Parent
		$db['parent'] = '';
		$db['slug'] = $key;
		$explodeKey = explode('/', $key);
		if(isset($explodeKey[1])) {
			$db['parent'] = $explodeKey[0];
			$db['slug'] = $explodeKey[1];
		}

		// Date
		if( !isset($db['date']) ) {
			$db['date'] = Date::current(DB_DATE_FORMAT);
		}

		// Status
		if( !isset($db['status']) ) {
			$db['status'] = CLI_STATUS;
		}

		// Owner username
		if( !isset($db['username']) ) {
			$db['username'] = CLI_USERNAME;
		}

		// New page or update page
		if($insert) {
			Log::set('CLI MODE'.LOG_SEP.'New page found, key:'.$key);
			return $this->add($db, $climode=true);
		} else {
			Log::set('CLI MODE'.LOG_SEP.'Different checksum, updating page, key:'.$key);
			return $this->edit($db, $climode=true);
		}
	}

	private function stylingFieldsForFile($field, $value)
	{
		// Support for Markdown files, good approach for Github
		if (FILENAME==='index.md') {
			if ($field==='title') {
				return '#Title: '.$value;
			} elseif ($field==='content') {
				return '---'.PHP_EOL.$value;
			} else {
				return '<!-- '.Text::firstCharUp($field).': '.$value.' -->';
			}
		}

		// Legacy style of Bludit with index.txt
		if ($field==='content') {
			return 'Content:'.PHP_EOL.$value;
		}
		return Text::firstCharUp($field).': '.$value;
	}

	// Returns the database
	public function getDB()
	{
		return $this->db;
	}

	// Returns an Array, array('tagSlug'=>'tagName')
	// (string) $tags, tag list separeted by comma.
	public function generateTags($tags)
	{
		$tmp = array();

		$tags = trim($tags);

		if(empty($tags)) {
			return $tmp;
		}

		// Make array
		$tags = explode(',', $tags);

		foreach($tags as $tag)
		{
			$tag = trim($tag);
			$tagKey = Text::cleanUrl($tag);
			$tmp[$tagKey] = $tag;
		}

		return $tmp;
	}

	// Change all posts with the old category key for the new category key
	public function changeCategory($oldCategoryKey, $newCategoryKey)
	{
		foreach($this->db as $key=>$value) {
			if($value['category']==$oldCategoryKey) {
				$this->db[$key]['category'] = $newCategoryKey;
			}
		}

		// Save database
		return $this->save();
	}

}