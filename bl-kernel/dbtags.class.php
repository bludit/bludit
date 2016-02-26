<?php defined('BLUDIT') or die('Bludit CMS.');

class dbTags extends dbJSON
{
	/*
		$postsIndex['tag1']['name'] = 'Tag 1';
		$postsIndex['tag1']['posts'] = array('post1','post2','post3');
		$postsIndex['tag2']['name'] = 'Tag 2';
		$postsIndex['tag2']['posts'] = array('post1','post5');
	*/
	public $dbFields = array(
		'postsIndex'=>array('inFile'=>false, 'value'=>array()),
		'pagesIndex'=>array('inFile'=>false, 'value'=>array())
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'tags.php');
	}

	// Returns an array with all tags names
	public function getAll()
	{
		$tmp = array();
		foreach($this->db['postsIndex'] as $tagSlug=>$tagInfo) {
			$tmp[$tagSlug] = $tagInfo['name'];
		}

		// Sort low to high, by value.
		natcasesort($tmp);

		return $tmp;
	}

	// Returns an array with a list of posts keys, filtered by a page number and a tag key.
	public function getList($pageNumber, $postPerPage, $tagKey)
	{
		if( !isset($this->db['postsIndex'][$tagKey]) ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying get the posts list by the tag key: '.$tagKey);
			return array();
		}

		$init = (int) $postPerPage * $pageNumber;
		$end  = (int) min( ($init + $postPerPage - 1), $this->countPostsByTag($tagKey) - 1 );
		$outrange = $init<0 ? true : $init > $end;

		if(!$outrange) {
			$list = $this->db['postsIndex'][$tagKey]['posts'];
			$tmp = array_flip($list); // Change the posts keys list in the array key.
			return array_slice($tmp, $init, $postPerPage, true);
		}

		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying get the list of posts, out of range?. Pagenumber: '.$pageNumber);
		return array();
	}

	public function countPostsByTag($tagKey)
	{
		if( isset($this->db['postsIndex'][$tagKey]) ) {
			return count($this->db['postsIndex'][$tagKey]['posts']);
		}

		return 0;
	}

	// Regenerate the posts index for each tag.
	// (array) $db, the $db must be sorted by date and the posts published only.
	public function reindexPosts($db)
	{
		$tagsIndex = array();

		// Foreach post
		foreach($db as $postKey=>$values)
		{
			$tags = $values['tags'];

			// Foreach tag from post
			foreach($tags as $tagKey=>$tagName)
			{
				if( isset($tagsIndex[$tagKey]) ) {
					array_push($tagsIndex[$tagKey]['posts'], $postKey);
				}
				else {
					$tagsIndex[$tagKey]['name'] = $tagName;
					$tagsIndex[$tagKey]['posts'] = array($postKey);
				}
			}
		}

		$this->db['postsIndex'] = $tagsIndex;

		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}

		return true;
	}

}