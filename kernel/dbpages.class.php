<?php defined('BLUDIT') or die('Bludit CMS.');

class dbPages extends dbJSON
{
	private $parentKeyList = array();

	private $dbFields = array(
		'title'=>		array('inFile'=>true,	'value'=>''),
		'content'=>		array('inFile'=>true,	'value'=>''),
		'description'=>		array('inFile'=>false,	'value'=>''),
		'username'=>		array('inFile'=>false,	'value'=>''),
		'tags'=>		array('inFile'=>false,	'value'=>''),
		'status'=>		array('inFile'=>false,	'value'=>'draft'),
		'date'=>		array('inFile'=>false,	'value'=>0),
		'position'=>		array('inFile'=>false,	'value'=>0)
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'pages.php');
	}

	public function add($args)
	{
		$dataForDb = array();	// This data will be saved in the database
		$dataForFile = array(); // This data will be saved in the file

		$key = $this->generateKey($args['slug'], $args['parent']);

		// The user is always the one loggued.
		$args['username'] = Session::get('username');
		if( Text::isEmpty($args['username']) ) {
			return false;
		}

		// Current date.
		if(empty($args['date'])) {
			$args['date'] = Date::current(DB_DATE_FORMAT);
		}

		// Verify arguments with the database fields.
		foreach($this->dbFields as $field=>$options)
		{
			if( isset($args[$field]) )
			{
				// Sanitize if will be saved on database.
				if( !$options['inFile'] ) {
					$tmpValue = Sanitize::html($args[$field]);
				}
				else {
					$tmpValue = $args[$field];
				}
			}
			// Default value for the field.
			else
			{
				$tmpValue = $options['value'];
			}

			// Check where the field will be written, in file or database.
			if($options['inFile']) {
				$dataForFile[$field] = Text::firstCharUp($field).': '.$tmpValue;
			}
			else
			{
				// Set type
				settype($tmpValue, gettype($options['value']));

				// Save on database
				$dataForDb[$field] = $tmpValue;
			}
		}

		// Make the directory. Recursive.
		if( Filesystem::mkdir(PATH_PAGES.$key, true) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the directory '.PATH_PAGES.$key);
			return false;
		}

		// Make the index.txt and save the file.
		$data = implode("\n", $dataForFile);
		if( file_put_contents(PATH_PAGES.$key.'/index.txt', $data) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to put the content in the file index.txt');
			return false;
		}

		// Save the database
		$this->db[$key] = $dataForDb;
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}

	public function edit($args)
	{
		$dataForDb = array();
		$dataForFile = array();

		$newKey = $this->generateKey($args['slug'], $args['parent'], false, $args['key']);

		// The user is always the one loggued.
		$args['username'] = Session::get('username');
		if( Text::isEmpty($args['username']) ) {
			return false;
		}

		// If the page is draft then the time created is now.
		if( $this->db[$args['key']]['status']=='draft' ) {
			$args['date'] = Date::current(DB_DATE_FORMAT);
		}
		else {
			$args['date'] = $this->db[$args['key']]['date'];
		}

		// Verify arguments with the database fields.
		foreach($this->dbFields as $field=>$options)
		{
			if( isset($args[$field]) )
			{
				// Sanitize if will be saved on database.
				if( !$options['inFile'] ) {
					$tmpValue = Sanitize::html($args[$field]);
				}
				else {
					$tmpValue = $args[$field];
				}
			}
			// Default value for the field.
			else
			{
				$tmpValue = $options['value'];
			}

			// Check where the field will be written, if in the file or in the database.
			if($options['inFile']) {
				$dataForFile[$field] = Text::firstCharUp($field).': '.$tmpValue;
			}
			else
			{
				// Set type
				settype($tmpValue, gettype($options['value']));

				// Save on database
				$dataForDb[$field] = $tmpValue;
			}
		}

		// Move the directory from old key to new key.
		if($newKey!==$args['key'])
		{
			if( Filesystem::mv(PATH_PAGES.$args['key'], PATH_PAGES.$newKey) === false ) {
				Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to move the directory to '.PATH_PAGES.$newKey);
				return false;
			}
		}

		// Make the index.txt and save the file.
		$data = implode("\n", $dataForFile);
		if( file_put_contents(PATH_PAGES.$newKey.DS.'index.txt', $data) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to put the content in the file index.txt');
			return false;
		}

		// Remove the old key.
		unset($this->db[$args['key']]);

		// Save the database
		$this->db[$newKey] = $dataForDb;
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}

	public function delete($key)
	{
		// Page doesn't exist in database.
		if(!$this->pageExists($key)) {
			Log::set(__METHOD__.LOG_SEP.'The page does not exist. Key: '.$key);
		}

		// Delete the index.txt file.
		if( Filesystem::rmfile(PATH_PAGES.$key.DS.'index.txt') === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the file index.txt');
		}

		// Delete the directory.
		if( Filesystem::rmdir(PATH_PAGES.$key) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the directory '.PATH_PAGES.$key);
		}

		// Remove from database.
		unset($this->db[$key]);

		// Save the database.
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
		}

		return true;
	}

	// Return an array with the database for a page, FALSE otherwise.
	public function getDb($key)
	{
		if($this->pageExists($key)) {
			return $this->db[$key];
		}

		return false;
	}

	// Return TRUE if the page exists, FALSE otherwise.
	public function pageExists($key)
	{
		return isset($this->db[$key]);
	}

	public function parentKeyList()
	{
		return $this->parentKeyList;
	}

	public function parentKeyExists($key)
	{
		return isset($this->parentKeyList[$key]);
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

		if($newKey!==$oldKey)
		{
			// Verify if the key is already been used.
			if( isset($this->db[$newKey]) )
			{
				if( !Text::endsWithNumeric($newKey) ) {
					$newKey = $newKey.'-0';
				}

				while( isset($this->db[$newKey]) ) {
					$newKey++;
				}
			}
		}

		if($returnSlug)
		{
			$explode = explode('/', $newKey);

			if(isset($explode[1])) {
				return $explode[1];
			}

			return $explode[0];
		}

		return $newKey;
	}

	// Return an array with all page's databases.
	public function getAll()
	{
		return $this->db;
	}

	public function regenerateCli()
	{
		$db = $this->db;
		$newPaths = array();
		$fields = array();

		// Default fields and value
		foreach($this->dbFields as $field=>$options) {
			if(!$options['inFile']) {
				$fields[$field] = $options['value'];
			}
		}

		$fields['status'] = CLI_STATUS;
		$fields['date'] = Date::current(DB_DATE_FORMAT);

		$tmpPaths = glob(PATH_PAGES.'*', GLOB_ONLYDIR);
		foreach($tmpPaths as $directory)
		{
			$key = basename($directory);

			if(file_exists($directory.DS.'index.txt')) {
				// The key is the directory name
				$newPaths[$key] = true;
			}

			// Recovery pages from subdirectories
			$subPaths = glob($directory.DS.'*', GLOB_ONLYDIR);
			foreach($subPaths as $subDirectory)
			{
				$subKey = basename($subDirectory);

				if(file_exists($subDirectory.DS.'index.txt')) {
					// The key is composed by the directory/subdirectory
					$newPaths[$key.'/'.$subKey] = true;
				}
			}
		}

		foreach($newPaths as $key=>$value)
		{
			if(!isset($this->db[$key])) {
				$this->db[$key] = $fields;
			}

			$Page = new Page($key);

			// Update all fields from FILE to DATABASE.
			foreach($fields as $f=>$v)
			{
				if($Page->getField($f)) {
					// DEBUG: Validar/Sanitizar valores, ej: validar formato fecha
					$this->db[$key][$f] = $Page->getField($f);
				}
			}

			// DEBUG: Update tags
		}

		// Remove old pages from db
		foreach( array_diff_key($db, $newPaths) as $key=>$data ) {
			unset($this->db[$key]);
		}

		// Save the database.
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return $this->db!=$db;
	}
}