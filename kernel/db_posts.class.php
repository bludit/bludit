<?php defined('BLUDIT') or die('Bludit CMS.');

class dbPosts extends DB_SERIALIZE
{
	function __construct()
	{
		parent::__construct(PATH_DATABASES.'posts.php');
	}

	// Return an array with the database for a post.
	public function getDb($slug)
	{
		return $this->vars['posts'][$slug];
	}

	// Return TRUE if the post exists, FALSE otherwise.
	public function validPost($slug)
	{	
		return isset($this->vars['posts'][$slug]);
	}

	public function regenerate()
	{
		$db = $this->vars['posts'];
		$paths = array();

		$fields = array(
			'title'=>'',
			'content'=>'',
			'username'=>'',
			'status'=>'draft',
			'author'=>'',
			'unixstamp'=>Date::unixstamp(),
			'date'=>'',
			'timeago'=>'',
			'slug'=>'',
			'permalink'=>''
		);

		if(HANDMADE_PUBLISHED)
			$fields['status']='published';

		// Scan all directories from /content/post/
		$tmpPaths = glob(PATH_POSTS.'*', GLOB_ONLYDIR);
		foreach($tmpPaths as $directory)
		{
			// Each directory is a post
			if(file_exists($directory.'/index.txt')) {
				$key = basename($directory);
				$paths[$key] = true;
			}
		}

		// Remove old posts from db
		foreach( array_diff_key($db, $paths) as $slug=>$data )
			unset($this->vars['posts'][$slug]);

		// Insert new posts to db
		foreach( array_diff_key($paths, $db) as $slug=>$data )
			$this->vars['posts'][$slug] = $fields;

		$this->save();
	}

	public function getPage($pageNumber, $postPerPage)
	{
		$init = (int) $postPerPage * $pageNumber;
		$end  = (int) min( ($init + $postPerPage - 1), count($this->vars['posts']) - 1 );

		$outrange = $init<0 ? true : $init > $end;

		// DEBUG: Ver una mejor manera de eliminar draft post antes de ordenarlos
		$this->removeUnpublished();

		$tmp = $this->sortByDate();

		if(!$outrange)
			return array_slice($tmp, $init, $end+1, true);

		return array();
	}

	// DEBUG: Ver una mejor manera de eliminar draft post antes de ordenarlos
	private function removeUnpublished()
	{
		$tmp = array();

		foreach($this->vars['posts'] as $key=>$value)
		{
			if($value['status']==='published')
				$tmp[$key]=$value;
		}

		$this->vars['posts'] = $tmp;
	}

	private function sortByDate($low_to_high=false)
	{
		// high to low
		function high_to_low($a, $b) {
			return $a['unixstamp']<$b['unixstamp'];
		}

		// low to high
		function low_to_high($a, $b) {
			return $a['unixstamp']>$b['unixstamp'];
		}

		$tmp = $this->vars['posts'];

		if($low_to_high)
			uasort($tmp, 'low_to_high');
		else
			uasort($tmp, 'high_to_low');

		return $tmp;
	}

}

?>
