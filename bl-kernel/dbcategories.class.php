<?php defined('BLUDIT') or die('Bludit CMS.');

/*
Database structure
- To re index the list of posts and pages need to be sorted

{
	"videos": {
		"name": "Videos",
		"posts": [ "first-post", "bull-terrier" ],
		"pages": [ "my-page", "second-page" ]
	},
	"pets": {
		"name": "Pets",
		"posts": [ "second-post", "bull-terrier" ],
		"pages": [ "cats-and-dogs" ]
	}
}
*/

class dbCategories extends dbJSON
{
	public $dbFields = array();

	function __construct()
	{
		parent::__construct(PATH_DATABASES.'categories.php');
	}

	private function getByCategory($type='posts', $categoryKey, $amountPerPage, $pageNumber)
	{
		// Check if the category exists
		if( !isset($this->db[$categoryKey]) ) {
			Log::set(__METHOD__.LOG_SEP.'Error getting '.$type.' by the category: '.$categoryKey);
			return array();
		}

		$list = $this->db[$categoryKey][$type];

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

	public function getPagesByCategory($categoryKey, $amountPerPage, $pageNumber)
	{
		return $this->getByCategory('pages', $categoryKey, $amountPerPage, $pageNumber);
	}

	public function getPostsByCategory($categoryKey, $amountPerPage, $pageNumber)
	{
		return $this->getByCategory('posts', $categoryKey, $amountPerPage, $pageNumber);
	}

	private function countByCategory($type='posts', $categoryKey)
	{
		if( isset($this->db[$categoryKey][$type]) ) {
			return count($this->db[$categoryKey][$type]);
		}

		return 0;
	}

	public function countPostsByCategory($categoryKey)
	{
		return $this->countByCategory('posts', $categoryKey);
	}

	public function countPagesByCategory($categoryKey)
	{
		return $this->countByCategory('pages', $categoryKey);
	}

	public function getAll()
	{
		$tmp = array();
		foreach($this->db as $key=>$data) {
			$tmp[$key] = $data['name'];
		}

		// Sort low to high, by value.
		natcasesort($tmp);

		return $tmp;
	}

	public function add($category)
	{
		$categoryKey = $this->generateKey($category);
		if( isset($this->db[$categoryKey]) ) {
			Log::set(__METHOD__.LOG_SEP.'The category already exist, key: '.$categoryKey.', name: '.$category);
			return false;
		}

		$this->db[$categoryKey]['name'] = $category;
		$this->db[$categoryKey]['posts'] = array();
		$this->db[$categoryKey]['pages'] = array();

		$this->save();

		return $categoryKey;
	}

	public function remove($categoryKey)
	{
		if( !isset($this->db[$categoryKey]) ) {
			Log::set(__METHOD__.LOG_SEP.'The category does not exist, key: '.$categoryKey);
			return false;
		}

		unset($this->db[$categoryKey]);

		return $this->save();
	}

	public function edit($oldCategoryKey, $newCategory)
	{
		$newCategoryKey = $this->generateKey($newCategory);
		if( isset($this->db[$newCategoryKey]) ) {
			Log::set(__METHOD__.LOG_SEP.'The category already exist, key: '.$newCategoryKey.', name: '.$newCategory);
			return false;
		}

		// Add the new category with the posts and pages from the old one
		$this->db[$newCategoryKey]['name'] = $newCategory;
		$this->db[$newCategoryKey]['posts'] = $this->db[$oldCategoryKey]['posts'];
		$this->db[$newCategoryKey]['pages'] = $this->db[$oldCategoryKey]['posts'];

		// Remove the old category
		unset( $this->db[$oldCategoryKey] );

		$this->save();

		return $newCategoryKey;
	}

	// Re-generate posts index
	// (array) $db, the $db must be sorted by date and the posts published only.
	public function reIndexPosts($db)
	{
		// Clean post list
		foreach( $this->db as $key=>$value ) {
			$this->db[$key]['posts'] = array();
		}

		// Foreach post in the database
		foreach($db as $postKey=>$postData) {
			if( !empty($postData['category']) ) {
				$categoryKey = $postData['category'];
				if( isset($this->db[$categoryKey]['posts']) ) {
					array_push($this->db[$categoryKey]['posts'], $postKey);
				}
			}
		}

		return $this->save();
	}

	// Re-generate pages index
	// (array) $db, the $db must be sorted by date and the posts published only.
	public function reIndexPages($db)
	{
		// Clean post list
		foreach( $this->db as $key=>$value ) {
			$this->db[$key]['pages'] = array();
		}

		// Foreach post in the database
		foreach($db as $postKey=>$postData) {
			if( !empty($postData['category']) ) {
				$categoryKey = $postData['category'];
				if( isset($this->db[$categoryKey]['pages']) ) {
					array_push($this->db[$categoryKey]['pages'], $postKey);
				}
			}
		}

		return $this->save();
	}

	public function exists($categoryKey)
	{
		return isset( $this->db[$categoryKey] );
	}

	public function getName($categoryKey)
	{
		return $this->db[$categoryKey]['name'];
	}

	public function generateKey($category)
	{
		return Text::cleanUrl($category);
	}

	public function getListOfPosts($pageNumber, $postPerPage, $categoryKey)
	{
		if( !isset($this->db[$categoryKey]) ) {
			Log::set(__METHOD__.LOG_SEP.'Error occurred when trying get the posts list by the category key: '.$categoryKey);
			return array();
		}

		$init = (int) $postPerPage * $pageNumber;
		$end  = (int) min( ($init + $postPerPage - 1), count($this->db[$categoryKey]['posts']) );
		$outrange = $init<0 ? true : $init > $end;

		if(!$outrange) {
			$list = $this->db[$categoryKey]['posts'];
			$tmp = array_flip($list); // Change the posts keys list in the array key.
			return array_slice($tmp, $init, $postPerPage, true);
		}

		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying get the list of posts, out of range?. Pagenumber: '.$pageNumber);
		return array();
	}

}