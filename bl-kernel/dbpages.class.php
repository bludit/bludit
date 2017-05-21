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
		'status'=>		array('inFile'=>false,	'value'=>'draft'), // published, draft, scheduled
		'date'=>		array('inFile'=>false,	'value'=>''),
		'dateModified'=>	array('inFile'=>false,	'value'=>''),
		'position'=>		array('inFile'=>false,	'value'=>0),
		'coverImage'=>		array('inFile'=>false,	'value'=>''),
		'category'=>		array('inFile'=>false,	'value'=>''),
		'md5file'=>		array('inFile'=>false,	'value'=>''),
		'uuid'=>		array('inFile'=>false,	'value'=>''),
		'allowComments'=>	array('inFile'=>false,	'value'=>false)
	);

	function __construct()
	{
		parent::__construct(DB_PAGES);
	}

	// Create a new page
	public function add($args)
	{
		$dataForDb = array();	// This data will be saved in the database
		$dataForFile = array(); // This data will be saved in the file

		// The user is always the one loggued
		$args['username'] = Session::get('username');
		if( Text::isEmpty($args['username']) ) {
			return false;
		}

		// Generate key
		$key = $this->generateKey($args['slug'], $args['parent']);

		// Generate UUID
		$args['uuid'] = md5( uniqid() );

		// Date
		$currentDate = Date::current(DB_DATE_FORMAT);

		// Validate date
		if(!Valid::date($args['date'], DB_DATE_FORMAT)) {
			$args['date'] = $currentDate;
		}

		// Schedule page
		if( ($args['date']>$currentDate) && ($args['status']=='published') ) {
			$args['status'] = 'scheduled';
		}

		foreach($this->dbFields as $field=>$options) {
			if( isset($args[$field]) ) {
				if($field=='tags') {
					$value = $this->generateTags($args['tags']);
				}
				else {
					if( !$options['inFile'] ) {
						// Sanitize if will be stored on database
						$value = Sanitize::html($args[$field]);
					}
					else {
						$value = $args[$field];
					}
				}
			}
			else {
				// Default value for the field
				$value = $options['value'];
			}

			// Where the data is stored
			if($options['inFile']) {
				$dataForFile[$field] = Text::firstCharUp($field).': '.$value;
			}
			else {
				// Set type
				settype($value, gettype($options['value']));

				// Save on database
				$dataForDb[$field] = $value;
			}
		}

		// Create the directory
		if( Filesystem::mkdir(PATH_PAGES.$key, true) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the directory '.PATH_PAGES.$key);
			return false;
		}

		// Make the index.txt and save the file.
		$data = implode("\n", $dataForFile);
		if( file_put_contents(PATH_PAGES.$key.DS.FILENAME, $data) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to put the content in the file '.FILENAME);
			return false;
		}

		// Insert in database
		$this->db[$key] = $dataForDb;

		// Sort database
		$this->sortBy();

		// Save database
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return $key;
	}

	public function edit($args)
	{
		$dataForDb = array();
		$dataForFile = array();

		// The user is always the one loggued
		$args['username'] = Session::get('username');
		if( Text::isEmpty($args['username']) ) {
			return false;
		}

		$newKey = $this->generateKey($args['slug'], $args['parent'], false, $args['key']);

		// If the page is draft then the time created is now
		if( $this->db[$args['key']]['status']=='draft' ) {
			$args['date'] = Date::current(DB_DATE_FORMAT);
		}
		else {
			$args['date'] = $this->db[$args['key']]['date'];
		}

		// Current UUID
		$args['uuid'] = $this->db[$args['key']]['uuid'];

		// Modified date
		$args['dateModified'] = Date::current(DB_DATE_FORMAT);

		foreach($this->dbFields as $field=>$options) {
			if( isset($args[$field]) ) {
				if($field=='tags') {
					$value = $this->generateTags($args['tags']);
				}
				else {
					if( !$options['inFile'] ) {
						// Sanitize if will be stored on database
						$value = Sanitize::html($args[$field]);
					}
					else {
						// Default value for the field
						$value = $args[$field];
					}
				}
			}
			else {
				$value = $options['value'];
			}

			// Where the data is stored
			if($options['inFile']) {
				$dataForFile[$field] = Text::firstCharUp($field).': '.$value;
			}
			else {
				// Set type
				settype($value, gettype($options['value']));

				// Save on database
				$dataForDb[$field] = $value;
			}
		}

		// Move the directory from old key to new key.
		if($newKey!==$args['key']) {
			if( Filesystem::mv(PATH_PAGES.$args['key'], PATH_PAGES.$newKey) === false ) {
				Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to move the directory to '.PATH_PAGES.$newKey);
				return false;
			}
		}

		// Make the index.txt and save the file.
		$data = implode("\n", $dataForFile);
		if( file_put_contents(PATH_PAGES.$newKey.DS.FILENAME, $data) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to put the content in the file '.FILENAME);
			return false;
		}

		// Remove the old key
		unset( $this->db[$args['key']] );

		// Insert in database
		$this->db[$newKey] = $dataForDb;

		// Sort database
		$this->sortBy();

		// Save database
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return $newKey;
	}

	public function delete($key)
	{
		// Page doesn't exist in database
		if(!$this->exists($key)) {
			Log::set(__METHOD__.LOG_SEP.'The page does not exist. Key: '.$key);
		}

		// Delete the index.txt file
		if( Filesystem::rmfile(PATH_PAGES.$key.DS.FILENAME) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the file '.FILENAME);
		}

		// Delete the directory
		if( Filesystem::rmdir(PATH_PAGES.$key) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the directory '.PATH_PAGES.$key);
		}

		// Remove from database
		unset($this->db[$key]);

		// Save the database.
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
		}

		return true;
	}

	// Returns a database with published pages
	public function getPublishedDB()
	{
		$tmp = $this->db;
		foreach($tmp as $key=>$fields) {
			if($fields['status']!='published') {
				unset($tmp[$key]);
			}
		}
		return $tmp;
	}

	// Return an array with the database for a page, FALSE otherwise.
	public function getPageDB($key)
	{
		if( $this->exists($key) ) {
			return $this->db[$key];
		}

		return false;
	}

	// Returns an array with a list of pages
	// (int) $pageNumber, the page number
	// (int) $amountOfItems, amount of items to return
	// (boolean) $onlyPublished, TRUE to return only published pages
	public function getList($pageNumber, $amountOfItems, $onlyPublished=true, $removeErrorPage=true)
	{
		if( $removeErrorPage ) {
			unset($this->db['error']);
		}

		$db = $this->db;

		if( $onlyPublished ) {
			$db = $this->getPublishedDB();
		}

		$total = count($db);
		$init = (int) $amountOfItems * $pageNumber;
		$end  = (int) min( ($init + $amountOfItems - 1), $total );
		$outrange = $init<0 ? true : $init>$end;

		if(!$outrange) {
			return array_slice($db, $init, $amountOfItems, true);
		}

		return array();
	}

	// Returns the amount of pages
	// (boolean) $total, TRUE returns the total of pages
	// (boolean) $total, FALSE returns the total of published pages (without draft and scheduled)
	public function count($onlyPublished=true)
	{
		if( $onlyPublished ) {
			$db = $this->getPublishedDB();
			return count($db);
		}

		return count($this->db);
	}

	public function getParents($onlyPublished=true)
	{
		if( $onlyPublished ) {
			$db = $this->getPublishedDB();
		}
		else {
			$db = $this->db;
		}

		foreach( $db as $key=>$fields ) {
			if( Text::stringContains($key, '/') ) {
				unset($db[$key]);
			}
		}

		return $db;
	}

	// Return TRUE if the page exists, FALSE otherwise.
	public function exists($key)
	{
		return isset( $this->db[$key] );
	}

	public function sortBy()
	{
		if( ORDER_BY=='date' ) {
			return $this->sortByDate(true);
		} else {
			return $this->sortByPosition(false);
		}
	}

	// Sort pages by position
	public function sortByPosition($HighToLow=false)
	{
		if($HighToLow) {
			uasort($this->db, array($this, 'sortByPositionHighToLow'));
		}
		else {
			uasort($this->db, array($this, 'sortByPositionLowToHigh'));
		}
		return true;
	}

	private function sortByPositionLowToHigh($a, $b) {
		return $a['position']>$b['position'];
	}
	private function sortByPositionHighToLow($a, $b) {
		return $a['position']<$b['position'];
	}

	// Sort pages by date
	public function sortByDate($HighToLow=true)
	{
		if($HighToLow) {
			uasort($this->db, array($this, 'sortByDateHighToLow'));
		}
		else {
			uasort($this->db, array($this, 'sortByDateLowToHigh'));
		}
		return true;
	}

	private function sortByDateLowToHigh($a, $b) {
		return $a['date']>$b['date'];
	}
	private function sortByDateHighToLow($a, $b) {
		return $a['date']<$b['date'];
	}

// ----- OLD

	// Set a field of the database
	public function setField($key, $field, $value)
	{
		if( $this->exists($key) ) {
			settype($value, gettype($this->dbFields[$key]['value']));
			$this->db[$key][$field] = $value;
		}

		return false;
	}



	public function parentKeyList()
	{
		return $this->parentKeyList;
	}

	public function parentKeyExists($key)
	{
		return isset( $this->parentKeyList[$key] );
	}

	public function addParentKey($key)
	{
		$this->parentKeyList[$key] = $key;
	}

	// Generate a valid Key/Slug.
	public function generateKey($text, $parent=NO_PARENT_CHAR, $returnSlug=false, $oldKey='')
	{
		if(Text::isEmpty($text)) {
			$text = 'empty';
		}

		if( Text::isEmpty($parent) || ($parent==NO_PARENT_CHAR) ) {
			$newKey = Text::cleanUrl($text);
		}
		else {
			$newKey = Text::cleanUrl($parent).'/'.Text::cleanUrl($text);
		}

		if($newKey!==$oldKey) {
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

		if($returnSlug) {
			$explode = explode('/', $newKey);

			if(isset($explode[1])) {
				return $explode[1];
			}

			return $explode[0];
		}

		return $newKey;
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

	// Return TRUE if there are new pages published, FALSE otherwise.
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
			elseif($fields['status']=='published') {
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

}
