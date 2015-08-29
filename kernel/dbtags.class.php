<?php defined('BLUDIT') or die('Bludit CMS.');

class dbTags extends dbJSON
{
	/*
		$postsIndex['tag1']['name'] = 'Tag 1';
		$postsIndex['tag1']['posts'] = array('post1','post2','post3');
		$postsIndex['tag2']['name'] = 'Tag 2';
		$postsIndex['tag2']['posts'] = array('post1','post5');
	*/
	private $dbFields = array(
		'postsIndex'=>array('inFile'=>false, 'value'=>array()),
		'pagesIndex'=>array('inFile'=>false, 'value'=>array())
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'tags.php');
	}

	public function getList($pageNumber, $postPerPage, $tagKey)
	{
		$init = (int) $postPerPage * $pageNumber;
		$end  = (int) min( ($init + $postPerPage - 1), $this->countPostsByTag($tagKey) - 1 );
		$outrange = $init<0 ? true : $init > $end;

		if(!$outrange) {
			$list = $this->db['postsIndex'][$tagKey]['posts'];
			$tmp = array_flip($list);
			return array_slice($tmp, $init, $postPerPage, true);
		}

		return array();
	}

	public function countPostsByTag($tagKey)
	{
		if( isset($this->db['postsIndex'][$tagKey]) ) {
			return count($this->db['postsIndex'][$tagKey]['posts']);
		}
		else {
			return 0;
		}
	}

	public function reindexPosts($db)
	{
		$tagsIndex = array();
		$currentDate = Date::current(DB_DATE_FORMAT);

		// Foreach post
		foreach($db as $postKey=>$values)
		{
			$explode = explode(',', $values['tags']);

			// Foreach tag from post
			foreach($explode as $tagName)
			{
				$tagName = trim($tagName);
				$tagKey = Text::cleanUrl($tagName);

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
	}

}