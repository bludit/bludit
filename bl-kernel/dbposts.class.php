<?php defined('BLUDIT') or die('Bludit CMS.');

class dbPosts extends dbJSON
{
	private $dbFields = array(
		'title'=>		array('inFile'=>true,	'value'=>''),
		'content'=>		array('inFile'=>true,	'value'=>''),
		'description'=>		array('inFile'=>false,	'value'=>''),
		'username'=>		array('inFile'=>false,	'value'=>''),
		'status'=>		array('inFile'=>false,	'value'=>'draft'), // published, draft, scheduled
		'tags'=>		array('inFile'=>false,	'value'=>array()),
		'allowComments'=>	array('inFile'=>false,	'value'=>0),
		'date'=>		array('inFile'=>false,	'value'=>''),
		'dateModified'=>	array('inFile'=>false,	'value'=>''),
		'coverImage'=>		array('inFile'=>false,	'value'=>''),
		'md5file'=>		array('inFile'=>false,	'value'=>'')
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'posts.php');
	}

	// Return the amount of posts
	// $total = TRUE, returns the total of posts
	// $total = FALSE, return the amount of published posts
	public function numberPost($total=false)
	{
		// Amount of total posts, published, scheduled and draft
		if($total) {
			return count($this->db);
		}

		// Amount of published posts
		$i = 0;
		foreach($this->db as $values) {
			if($values['status']=='published') {
				$i++;
			}
		}

		return $i;
	}

	// Returns the database
	public function getDB()
	{
		return $this->db;
	}

	// Return an array with the post's database, FALSE otherwise.
	public function getPostDB($key)
	{
		if($this->postExists($key)) {
			return $this->db[$key];
		}

		return false;
	}

	public function setPostDb($key, $field, $value)
	{
		if($this->postExists($key)) {
			$this->db[$key][$field] = $value;
		}

		return false;
	}

	// Return TRUE if the post exists, FALSE otherwise.
	public function postExists($key)
	{
		return isset( $this->db[$key] );
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

		// Current date, format of DB_DATE_FORMAT
		$currentDate = Date::current(DB_DATE_FORMAT);

		// Generate the database key / index
		$key = $this->generateKey($args['slug']);

		// The user is always who is loggued
		$args['username'] = Session::get('username');
		if( Text::isEmpty($args['username']) ) {
			Log::set(__METHOD__.LOG_SEP.'Session username doesnt exists.');
			return false;
		}

		// If the date is not valid, then set the current date.
		if(!Valid::date($args['date'], DB_DATE_FORMAT)) {
			$args['date'] = $currentDate;
		}

		// Schedule post ?
		if( ($args['date']>$currentDate) && ($args['status']=='published') ) {
			$args['status'] = 'scheduled';
		}

		// Verify arguments with the database fields.
		foreach($this->dbFields as $field=>$options)
		{
			// If the field is in the arguments
			if( isset($args[$field]) )
			{
				if($field=='tags') {
					$tmpValue = $this->generateTags($args['tags']);
				}
				else {
					// Sanitize if will be saved on database.
					if( !$options['inFile'] ) {
						$tmpValue = Sanitize::html($args[$field]);
					}
					else {
						$tmpValue = $args[$field];
					}
				}
			}
			// Set a default value if not in the arguments
			else
			{
				$tmpValue = $options['value'];
			}

			// Check where the field will be written, in the file or in the database
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
		if( file_put_contents(PATH_POSTS.$key.DS.FILENAME, $data) === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to put the content in the file index.txt');
			return false;
		}

		// Calculate the checksum of the file
		$dataForDb['md5file'] = md5_file(PATH_POSTS.$key.DS.FILENAME);

		// Save the database
		$this->db[$key] = $dataForDb;

		// Sort posts before save
		$this->sortByDate();

		if( $this->save() === false ) {

			// Trying to rollback
			Log::set(__METHOD__.LOG_SEP.'Rollback...');
			Filesystem::rmfile(PATH_POSTS.$key.DS.FILENAME);
			Filesystem::rmdir(PATH_POSTS.$key);

			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return $key;
	}

	public function edit($args)
	{
		if( $this->delete($args['key']) ) {

			// Modified date
			$args['dateModified'] = Date::current(DB_DATE_FORMAT);

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
		if( Filesystem::rmfile(PATH_POSTS.$key.DS.FILENAME) === false ) {
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

	// Returns an array with a list of posts keys, filtered by a page number.
	public function getList($pageNumber, $postPerPage, $removeUnpublished=true)
	{
		$totalPosts = $this->numberPost(true);

		// Remove the unpublished posts.
		if($removeUnpublished) {
			$this->removeUnpublished();
			$totalPosts = $this->numberPost(true);
		}

		$init = (int) $postPerPage * $pageNumber;
		$end  = (int) min( ($init + $postPerPage - 1), $totalPosts - 1 );
		$outrange = $init<0 ? true : $init>$end;

		if(!$outrange) {
			$tmp = array_slice($this->db, $init, $postPerPage, true);

			// Restore the database because we deleted the unpublished posts.
			$this->restoreDB();

			return $tmp;
		}

		return array();
	}

	// Delete all posts from an user
	public function deletePostsByUser($username)
	{
		foreach($this->db as $key=>$value) {

			if($value['username']==$username) {
				$this->delete($key);
				Log::set(__METHOD__.LOG_SEP.'Post deleted: '.$key);
			}
		}

		Log::set(__METHOD__.LOG_SEP.'Posts from the user '.$username.' were delete.');
		return true;
	}

	// Link-up all posts from an user to another user.
	public function linkPostsToUser($oldUsername, $newUsername)
	{
		foreach($this->db as $key=>$value) {

			if($value['username']==$oldUsername) {
				$this->db[$key]['username'] = $newUsername;
			}
		}

		// Sort posts before save.
		$this->sortByDate();

		// Save the database.
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		Log::set(__METHOD__.LOG_SEP.'Posts linked to another user.');
		return true;
	}

	// Remove unpublished posts, status != published.
	public function removeUnpublished()
	{
		foreach($this->db as $key=>$values) {

			if($values['status']!='published') {
				unset($this->db[$key]);
			}
		}

		return true;
	}

	// Return TRUE if there are new posts published, FALSE otherwise.
	public function scheduler()
	{
		// Get current date
		$currentDate = Date::current(DB_DATE_FORMAT);

		$saveDatabase = false;

		// Check scheduled posts
		foreach($this->db as $postKey=>$values)
		{
			if($values['status']=='scheduled') {

				// Publish post
				if($values['date']<=$currentDate) {

					$this->db[$postKey]['status'] = 'published';
					$saveDatabase = true;
				}
			}
			elseif($values['status']=='published') {
				break;
			}
		}

		// Save the database ?
		if($saveDatabase)
		{
			if( $this->save() === false ) {
				Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
				return false;
			}

			Log::set(__METHOD__.LOG_SEP.'New posts published from the scheduler.');
			return true;
		}

		return false;
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

	// Sort posts by date.
	public function sortByDate($HighToLow=true)
	{
		if($HighToLow) {
			uasort($this->db, array($this, 'sortHighToLow'));
		}
		else {
			uasort($this->db, array($this, 'sortLowToHigh'));
		}

		return true;
	}

	private function sortLowToHigh($a, $b) {
		return $a['date']>$b['date'];
	}

	private function sortHighToLow($a, $b) {
		return $a['date']<$b['date'];
	}

	public function cliMode()
	{
		// LOG
		Log::set('CLI MODE - POSTS - Starting...');

		$postList = array();

		$postsDirectories = Filesystem::listDirectories(PATH_POSTS);

		foreach( $postsDirectories as $directory ) {

			if( Sanitize::pathFile($directory.DS.FILENAME) ) {

				// The key is the directory name
				$key = basename($directory);

				// Add the key to the list
				$postList[$key] = true;

				// Checksum
				$checksum = md5_file($directory.DS.FILENAME);

				// LOG
				Log::set('CLI MODE - Post found, key: '.$key);

				if( !isset($this->db[$key]) ) {

					// LOG
					Log::set('CLI MODE - The post is not in the database, key: '.$key);

					// Insert new post
					$this->cliModeInsert($key);
				}
				else {
					// If checksum is different, update the post
					if( $this->db[$key]['md5file']!==$checksum ) {

						// LOG
						Log::set('CLI MODE - Different md5 checksum, key: '.$key);

						// Update the post
						$this->cliModeInsert($key, $update=true);
					}
				}
			}
		}

		// LOG
		Log::set('CLI MODE - Cleaning database...');

		foreach( array_diff_key($this->db, $postList) as $key=>$data ) {
			// LOG
			Log::set('CLI MODE - Removing post from database, key: '.$key);

			// Removing the post from database
			unset( $this->db[$key] );
		}

		// Sort posts before save
		$this->sortByDate();

		// Save the database
		$this->save();

		// LOG
		Log::set('CLI MODE - POSTS - Finishing...');

		return true;
	}

	private function cliModeInsert($key, $update=false)
	{
		if($update) {
			// LOG
			Log::set('CLI MODE - cliModeInsert() - Updating the post, key: '.$key);

			// Database from the current database
			$dataForDb = $this->db[$key];
			$dataForDb['dateModified'] = Date::current(DB_DATE_FORMAT);
		}
		else {
			// LOG
			Log::set('CLI MODE - cliModeInsert() - Inserting the new post, key: '.$key);

			// Database for the new post, fields with the default values
			$dataForDb = array();
			foreach( $this->dbFields as $field=>$options ) {

				if( !$options['inFile'] ) {
					$dataForDb[$field] = $options['value'];
				}
			}

			// Fields and value predefined in init.php
			$dataForDb['username']	= CLI_USERNAME;
			$dataForDb['status'] 	= CLI_STATUS;
			$dataForDb['date'] 	= Date::current(DB_DATE_FORMAT);
		}

		// MD5 checksum
		$dataForDb['md5file'] = md5_file(PATH_POSTS.$key.DS.FILENAME);

		// Generate the Object from the file
		$Post = new Post($key);

		foreach( $this->dbFields as $field=>$options ) {

			if( !$options['inFile'] ) {

				// Get the field from the file
				// If the field doesn't exist, the function returns FALSE
				$data = $Post->getField($field);

				if( $data!==false ) {

					$tmpValue = '';

					if( $field=='tags' ) {
						$tmpValue = $this->generateTags($data);
					}
					elseif( $field=='date' ) {

						// Validate format date from file
						if( Valid::date($data, DB_DATE_FORMAT) ) {

							$tmpValue = $data;

							if( $data > $currentDate ) {
								$dataForDb['status'] = 'scheduled';
							}
						}
					}
					else {
						$tmpValue = Sanitize::html($data);
					}

					settype($tmpValue, gettype($options['value']));
					$dataForDb[$field] = $tmpValue;
				}
			}
		}

		// Insert row in the database
		$this->db[$key] = $dataForDb;

		return true;
	}

}
