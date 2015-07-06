<?php defined('BLUDIT') or die('Bludit CMS.');

class dbPosts extends dbJSON
{
	private $dbFields = array(
		'title'=>			array('inFile'=>true, 'value'=>''),
		'content'=>			array('inFile'=>true, 'value'=>''),
		'description'=>		array('inFile'=>false, 'value'=>''),
		'username'=>		array('inFile'=>false, 'value'=>''),
		'status'=>			array('inFile'=>false, 'value'=>'draft'),
		'tags'=>			array('inFile'=>false, 'value'=>''),
		'allowComments'=>	array('inFile'=>false, 'value'=>false),
		'unixTimeCreated'=>	array('inFile'=>false, 'value'=>0),
		'unixTimeModified'=>array('inFile'=>false, 'value'=>0)
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'posts.php');
	}

	// Return an array with the database for a page, FALSE otherwise.
	public function getDb($key)
	{
		if($this->postExists($key)) {
			return $this->db[$key];
		}

		return false;
	}

	// Return TRUE if the post exists, FALSE otherwise.
	public function postExists($key)
	{
		return isset($this->db[$key]);
	}

	// Generate a valid Key/Slug.
	public function generateKey($text, $oldKey='')
	{
		if(Text::isEmpty($text)) {
			$text = 'empty';
		}

		$newKey = Text::cleanUrl($text);

		if($newKey===$oldKey) {
			return $newKey;
		}

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

		return $newKey;
	}

	public function add($args)
	{
		$dataForDb = array();	// This data will be saved in the database
		$dataForFile = array(); // This data will be saved in the file

		// Generate the database key.
		$key = $this->generateKey($args['slug']);

		// The user is always the one loggued.
		$args['username'] = Session::get('username');
		if( Text::isEmpty($args['username']) ) {
			return false;
		}

		// The current unix time stamp.
		if(empty($args['unixTimeCreated'])) {
			$args['unixTimeCreated'] = Date::unixTime();
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

		// Make the directory.
		if( Filesystem::mkdir(PATH_POSTS.$key) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to create the directory '.PATH_POSTS.$key);
			return false;
		}

		// Make the index.txt and save the file.
		$data = implode("\n", $dataForFile);
		if( file_put_contents(PATH_POSTS.$key.DS.'index.txt', $data) === false ) {
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
		// Unix time created and modified.
		$args['unixTimeCreated'] = $this->db[$args['key']]['unixTimeCreated'];
		$args['unixTimeModified'] = Date::unixTime();

		if( $this->delete($args['key']) ) {
			return $this->add($args);
		}

		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the post.');
		return false;
	}

	public function delete($key)
	{
		// Post doesn't exist in database.
		if(!$this->postExists($key)) {
			Log::set(__METHOD__.LOG_SEP.'The post does not exist. Key: '.$key);
		}

		// Delete the index.txt file.
		if( Filesystem::rmfile(PATH_POSTS.$key.DS.'index.txt') === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the file index.txt');
		}

		// Delete the directory.
		if( Filesystem::rmdir(PATH_POSTS.$key) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to delete the directory '.PATH_POSTS.$key);
		}

		// Remove from database.
		unset($this->db[$key]);

		// Save the database.
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
		}

		return true;
	}

	public function regenerate()
	{
		$db = $this->db;
		$paths = array();
		$fields = array();

		// Default fields and value
		foreach($this->dbFields as $field=>$options) {
			if(!$options['inFile']) {
				$fields[$field] = $options['value'];
			}
		}

		// Unix time stamp
		$fields['unixTimeCreated'] = Date::unixTime();

		// Username
		$fields['username'] = 'admin';

		if(HANDMADE_PUBLISHED) {
			$fields['status']='published';
		}

		// Recovery pages from the first level of directories
		$tmpPaths = glob(PATH_POSTS.'*', GLOB_ONLYDIR);
		foreach($tmpPaths as $directory)
		{
			$key = basename($directory);

			if(file_exists($directory.DS.'index.txt')) {
				// The key is the directory name
				$paths[$key] = true;
			}
		}

		// Remove old posts from db
		foreach( array_diff_key($db, $paths) as $slug=>$data ) {
			unset($this->db[$slug]);
		}

		// Insert new posts to db
		foreach( array_diff_key($paths, $db) as $slug=>$data ) {
			$this->db[$slug] = $fields;
		}

		// Save the database.
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return $this->db!=$db;
	}

	public function getPage($pageNumber, $postPerPage, $draftPosts=false)
	{
		$init = (int) $postPerPage * $pageNumber;
		$end  = (int) min( ($init + $postPerPage - 1), count($this->db) - 1 );

		$outrange = $init<0 ? true : $init > $end;

		// DEBUG: Ver una mejor manera de eliminar draft post antes de ordenarlos
		// DEBUG: Se eliminan antes de ordenarlos porque sino los draft cuentan como publicados en el PostPerPage.
		if(!$draftPosts){
			$this->removeUnpublished();
		}

		$tmp = $this->sortByDate();

		if(!$outrange) {
			return array_slice($tmp, $init, $end+1, true);
		}

		return array();
	}

	// DEBUG: Ver una mejor manera de eliminar draft post antes de ordenarlos
	private function removeUnpublished()
	{
		$tmp = array();

		foreach($this->db as $key=>$value)
		{
			if($value['status']==='published') {
				$tmp[$key]=$value;
			}
		}

		$this->db = $tmp;
	}

	private function sortByDate($low_to_high=false)
	{
		// high to low
		function high_to_low($a, $b) {
			return $a['unixTimeCreated']<$b['unixTimeCreated'];
		}

		// low to high
		function low_to_high($a, $b) {
			return $a['unixTimeCreated']>$b['unixTimeCreated'];
		}

		$tmp = $this->db;

		if($low_to_high)
			uasort($tmp, 'low_to_high');
		else
			uasort($tmp, 'high_to_low');

		return $tmp;
	}

}
