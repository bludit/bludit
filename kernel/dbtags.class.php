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

	public function countPostsByTag($tagKey)
	{
		if( isset($this->db['postsIndex'][$tagKey]) ) {
			return count($this->db['postsIndex'][$tagKey]['posts']);
		}
		else {
			return false;
		}
	}

	public function reindexPosts($db)
	{
		$tagsIndex = array();
		$currentDate = Date::current(DB_DATE_FORMAT);

		// Foreach post
		foreach($db as $postKey=>$values)
		{
			if( ($values['status']==='published') && ($values['date']<=$currentDate) )
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
		}

		$this->db['postsIndex'] = $tagsIndex;
		if( $this->save() === false ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying to save the database file.');
			return false;
		}
	}

}