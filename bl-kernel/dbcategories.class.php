<?php defined('BLUDIT') or die('Bludit CMS.');

/*
Database structure
- To re index the list of posts and pages need to be sorted

{
	"postsIndex": {
		"videos": {
			"name": "Videos",
			"list": [ "first-post", "second-post" ]
		},
		"pets": {
			"name": "Pets",
			"list": [ "second-post", "another-post" ]
		}
	},
	"pagesIndex": {
		"videos": {
			"name": "Videos",
			"list": [ "first-post", "second-post" ]
		},
		"music": {
			"name": "Music",
			"list": [ "second-post", "another-post" ]
		}
	}
}

*/

class dbCategories extends dbJSON
{
	public $dbFields = array(
		'postsIndex'=>array('inFile'=>false, 'value'=>array()),
		'pagesIndex'=>array('inFile'=>false, 'value'=>array())
	);

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'categories.php');
	}

	private function getByCategory($type='postsIndex', $categorySlug, $amountPerPage, $pageNumber)
	{
		// Check if the category exists
		if( !isset($this->db[$type][$categorySlug]) ) {
			Log::set(__METHOD__.LOG_SEP.'Error getting '.$type.' by the category: '.$categorySlug);
			return array();
		}

		$list = $this->db[$type][$categorySlug]['list'];

		$init = (int) $amountPerPage * $pageNumber;
		$end  = (int) min( ($init + $amountPerPage - 1), count($list) - 1 );
		$outrange = $init<0 ? true : $init > $end;

		if($outrange) {
			Log::set(__METHOD__.LOG_SEP.'Error getting '.$type.' by the category, out of range, pageNumber: '.$pageNumber);
			return array();
		}

		$tmp = array_flip($list);
		return array_slice($tmp, $init, $amountPerPage, true);
	}

	public function getPagesByCategory($categorySlug, $amountPerPage, $pageNumber)
	{
		return $this->getByCategory('pagesIndex', $categorySlug, $amountPerPage, $pageNumber);
	}

	public function getPostsByCategory($categorySlug, $amountPerPage, $pageNumber)
	{
		return $this->getByCategory('postsIndex', $categorySlug, $amountPerPage, $pageNumber);
	}

	private function countByCategory($type='postsIndex', $categorySlug)
	{
		if( isset($this->db[$type][$categorySlug]) ) {
			return count($this->db[$type][$categorySlug]['list']);
		}

		return 0;
	}

	public function countPostsByCategory($categorySlug)
	{
		return $this->countByCategory('postsIndex', $categorySlug);
	}

	public function countPagesByCategory($categorySlug)
	{
		return $this->countByCategory('pagesIndex', $categorySlug);
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