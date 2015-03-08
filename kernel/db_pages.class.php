<?php defined('BLUDIT') or die('Bludit CMS.');

class dbPages extends DB_SERIALIZE
{
	function __construct()
	{
		parent::__construct(PATH_DATABASES.'pages.php');
	}

	// Return an array with the database for a page
	public function getDb($slug)
	{
		return $this->vars['pages'][$slug];
	}

	// Return TRUE if the page exists, FALSE otherwise.
	public function validPage($slug)
	{	
		return isset($this->vars['pages'][$slug]);
	}

	// Return an array with all page's databases.
	public function getAll()
	{
		return $this->vars['pages'];
	}

	public function regenerate()
	{
		$db = $this->vars['pages'];
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
			'permalink'=>'',
			'parent'=>''
		);

		if(HANDMADE_PUBLISHED)
			$fields['status']='published';

		// Recovery pages from the first level of directories
		$tmpPaths = glob(PATH_PAGES.'*', GLOB_ONLYDIR);
		foreach($tmpPaths as $directory)
		{
			$key = basename($directory);

			if(file_exists($directory.'/index.txt'))
				// The key is the directory name
				$paths[$key] = true;

			// Recovery pages from subdirectories
			$subPaths = glob($directory.'/*', GLOB_ONLYDIR);
			foreach($subPaths as $subDirectory)
			{
				$subKey = basename($subDirectory);

				if(file_exists($subDirectory.'/index.txt'))
					// The key is composed by the directory/subdirectory
					$paths[$key.'/'.$subKey] = true;
			}
		}
		
		// Remove old posts from db
		foreach( array_diff_key($db, $paths) as $slug=>$data )
			unset($this->vars['pages'][$slug]);

		// Insert new posts to db
		foreach( array_diff_key($paths, $db) as $slug=>$data )
		{
			$this->vars['pages'][$slug] = $fields;

			// Get the parent if exists
			$explode = explode('/', $slug);
			if(!empty($explode[1]))
				$this->vars['pages'][$slug]['parent'] = $explode[0];
		}

		$this->save();
	}

}

?>
