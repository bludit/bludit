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
		'allowComments'=>	array('inFile'=>false,	'value'=>false),
		'date'=>		array('inFile'=>false,	'value'=>''),
		'coverImage'=>		array('inFile'=>false,	'value'=>''),
		'checksum'=>		array('inFile'=>false,	'value'=>'')
	);

	private $numberPosts = array(
		'total'=>0,
		'published'=>0
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

		return $this->numberPosts['published'];
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
		$currentDate = Date::current(DB_DATE_FORMAT);

		// Generate the database key.
		$key = $this->generateKey($args['slug']);

		// The user is always who is loggued.
		$args['username'] = Session::get('username');
		if( Text::isEmpty($args['username']) ) {
			Log::set(__METHOD__.LOG_SEP.'The session does not have the username.');
			return false;
		}

		// If the date not valid, then set the current date.
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
			// If the field is in the arguments.
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
			// Default value if not in the arguments.
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

		// Create Hash
		$serialize = serialize($dataForDb+$dataForFile);
		$dataForDb['checksum'] = sha1($serialize);

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

		// Sort posts before save.
		$this->sortByDate();

		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return $key;
	}

	public function edit($args)
	{
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

	// Returns an array with a list of posts keys, filtered by a page number.
	public function getList($pageNumber, $postPerPage, $removeUnpublished=true)
	{
		$totalPosts = $this->numberPosts['total'];

		// Remove the unpublished posts.
		if($removeUnpublished) {
			$this->removeUnpublished();
			$totalPosts = $this->numberPosts['published'];
		}

		$init = (int) $postPerPage * $pageNumber;
		$end  = (int) min( ($init + $postPerPage - 1), $totalPosts - 1 );
		$outrange = $init<0 ? true : $init>$end;

		if(!$outrange) {
			$tmp = array_slice($this->db, $init, $postPerPage, true);

			// Restore the database because we delete the unpublished posts.
			$this->restoreDB();

			return $tmp;
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

		Log::set(__METHOD__.LOG_SEP.'Posts from the user '.$username.' were delete.');
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
		foreach($this->db as $key=>$values)
		{
			if($values['status']!='published') {
				unset($this->db[$key]);
			}
		}

		$this->numberPosts['published'] = count($this->db);

		return true;
	}

	// Return TRUE if there are new posts published, FALSE otherwise.
	public function scheduler()
	{
		// Get current date.
		$currentDate = Date::current(DB_DATE_FORMAT);

		$saveDatabase = false;

		// Check scheduled posts
		foreach($this->db as $postKey=>$values)
		{
			if($values['status']=='scheduled')
			{
				// Publish post.
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

			Log::set(__METHOD__.LOG_SEP.'New post published from scheduler.');
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

	// Return TRUE if there are new posts or orphan post deleted, FALSE otherwise.
	public function regenerateCli()
	{
		$db = $this->db;
		$allPosts = array();
		$fields = array();
		$currentDate = Date::current(DB_DATE_FORMAT);

		// Generate default fields and values.
		foreach($this->dbFields as $field=>$options) {
			if(!$options['inFile']) {
				$fields[$field] = $options['value'];
			}
		}

		$fields['status'] = CLI_STATUS;
		$fields['date'] = $currentDate;
		$fields['username'] = CLI_USERNAME;

		// Get all posts from the first level of directories.
		$tmpPaths = Filesystem::listDirectories(PATH_POSTS);
		foreach($tmpPaths as $directory)
		{
			// Check if the post have the index.txt file.
			if(Sanitize::pathFile($directory.DS.'index.txt'))
			{
				// The key is the directory name.
				$key = basename($directory);

				$allPosts[$key] = true;

				// Create the new entry if not exist inside the DATABASE.
				if(!isset($this->db[$key])) {
					// New entry on database with the default fields and values.
					$this->db[$key] = $fields;
				}

				// Create the post from FILE.
				$Post = new Post($key);

				// Update all fields from FILE to DATABASE.
				foreach($fields as $f=>$v)
				{
					// If the field exists on the FILE, update it.
					if($Post->getField($f))
					{
						$valueFromFile = $Post->getField($f);

						Log::set(__METHOD__.LOG_SEP.'Field from file: '.$f);

						if($f=='tags') {
							// Generate tags array.
							$this->db[$key]['tags'] = $this->generateTags($valueFromFile);
						}
						elseif($f=='date') {
							// Validate Date from file
							if(Valid::date($valueFromFile, DB_DATE_FORMAT)) {
								$this->db[$key]['date'] = $valueFromFile;

								if( $valueFromFile > $currentDate ) {
									$this->db[$key]['status'] = 'scheduled';
								}
							}
						}
						else {
							// Sanitize the values from file.
							$this->db[$key][$f] = Sanitize::html($valueFromFile);
						}
					}
				}
			}
		}

		// Remove orphan posts from db, the orphan posts are posts deleted by hand (directory deleted).
		foreach( array_diff_key($db, $allPosts) as $key=>$data ) {
			unset($this->db[$key]);
		}

		// Sort posts before save.
		$this->sortByDate();

		// Save the database.
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		if($this->db!=$db) {
			Log::set(__METHOD__.LOG_SEP.'New posts added from Cli Mode');
		}

		return $this->db!=$db;
	}

}