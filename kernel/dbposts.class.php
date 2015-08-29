<?php defined('BLUDIT') or die('Bludit CMS.');

class dbPosts extends dbJSON
{
	private $dbFields = array(
		'title'=>		array('inFile'=>true,	'value'=>''),
		'content'=>		array('inFile'=>true,	'value'=>''),
		'description'=>		array('inFile'=>false,	'value'=>''),
		'username'=>		array('inFile'=>false,	'value'=>''),
		'status'=>		array('inFile'=>false,	'value'=>'draft'),
		'tags'=>		array('inFile'=>false,	'value'=>''),
		'allowComments'=>	array('inFile'=>false,	'value'=>false),
		'date'=>		array('inFile'=>false,	'value'=>'')
	);

	private $numberPosts = array(
		'total'=>0,
		'withoutDrafts'=>0
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'posts.php');

		$this->numberPosts['total'] = count($this->db);
	}

	public function numberPost($total=false)
	{
		if($total) {
			return $this->numberPosts['total'];
		}

		return $this->numberPosts['withoutDrafts'];
	}

	// Return an array with the post's database, FALSE otherwise.
	public function getDb($key)
	{
		if($this->postExists($key)) {
			return $this->db[$key];
		}

		return false;
	}

	public function setDb($key, $field, $value)
	{
		if($this->postExists($key)) {
			$this->db[$key][$field] = $value;
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

		// The user is always the who is loggued.
		$args['username'] = Session::get('username');
		if( Text::isEmpty($args['username']) ) {
			return false;
		}

		if(!Valid::date($args['date'], DB_DATE_FORMAT)) {
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
		if( !Valid::date($args['date'], DB_DATE_FORMAT) ) {
			$args['date'] = Date::current(DB_DATE_FORMAT);
		}

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

		// Recovery pages from the first level of directories
		$tmpPaths = glob(PATH_POSTS.'*', GLOB_ONLYDIR);
		foreach($tmpPaths as $directory)
		{
			$key = basename($directory);

			if(file_exists($directory.DS.'index.txt')) {
				// The key is the directory name
				$newPaths[$key] = true;
			}
		}

		foreach($newPaths as $key=>$value)
		{
			if(!isset($this->db[$key])) {
				$this->db[$key] = $fields;
			}

			$Post = new Post($key);

			// Update all fields from FILE to DATABASE.
			foreach($fields as $f=>$v)
			{
				if($Post->getField($f)) {
					// DEBUG: Validar/Sanitizar valores, ej: validar formato fecha
					$this->db[$key][$f] = $Post->getField($f);
				}
			}

			// DEBUG: Update tags
		}

		// Remove old posts from db
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

/*
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
		$fields['date'] = Date::current(DB_DATE_FORMAT);

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
*/
	public function getList($pageNumber, $postPerPage, $draftPosts=false)
	{
		// DEBUG: Ver una mejor manera de eliminar draft post antes de ordenarlos
		// DEBUG: Se eliminan antes de ordenarlos porque sino los draft cuentan como publicados en el PostPerPage.
		if(!$draftPosts) {
			$this->removeUnpublished();
			$this->numberPosts['withoutDrafts'] = count($this->db);
		}

		$init = (int) $postPerPage * $pageNumber;
		$end  = (int) min( ($init + $postPerPage - 1), count($this->db) - 1 );
		$outrange = $init<0 ? true : $init>$end;

		// Sort posts
		$this->sortByDate();

		if(!$outrange) {
			$tmp = $this->db;
			return array_slice($tmp, $init, $postPerPage, true);
		}

		return array();
	}

	// Delete all posts from an user.
	public function deletePostsByUser($username)
	{
		foreach($this->db as $key=>$value)
		{
			if($value['username']==$username) {
				unset($this->db[$key]);
			}
		}

		// Save the database.
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}

	// Link-up all posts from an user to another user.
	public function linkPostsToUser($oldUsername, $newUsername)
	{
		foreach($this->db as $key=>$value)
		{
			if($value['username']==$oldUsername) {
				$this->db[$key]['username'] = $newUsername;
			}
		}

		// Save the database.
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}

	// Remove the posts not published, status != published and date grater than current date.
	// DEBUG: Ver una mejor manera de eliminar draft post antes de ordenarlos
	private function removeUnpublished()
	{
		$tmp = array();
		$currentDate = Date::current(DB_DATE_FORMAT);

		foreach($this->db as $key=>$values)
		{
			if( ($values['status']==='published') && ($values['date']<=$currentDate) ) {
				$tmp[$key]=$values;
			}
		}

		$this->db = $tmp;
	}

	private function sortByDate($low_to_high=false)
	{
		// high to low
		function high_to_low($a, $b) {
			return $a['date']<$b['date'];
		}

		// low to high
		function low_to_high($a, $b) {
			return $a['date']>$b['date'];
		}

		$tmp = $this->db;

		if($low_to_high)
			uasort($tmp, 'low_to_high');
		else
			uasort($tmp, 'high_to_low');

		return true;
	}

}
