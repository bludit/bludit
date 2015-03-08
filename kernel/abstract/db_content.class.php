<?php

class Content
{
	public $vars;
	public $path;
	
	function __construct($slug)
	{
		if($this->build($slug)===false)
			$this->vars = false;
	}

	// Return true if valid post
	public function valid()
	{
		return($this->vars!==false);
	}

	public function get_field($field)
	{
		if(isset($this->vars[$field]))
			return $this->vars[$field];
		return false;
	}

	// $notoverwrite true if you don't want to replace the value if are set previusly
	public function setField($field, $value, $overwrite=true)
	{
		if($overwrite || empty($this->vars[$field]))
		{
			$this->vars[$field] = $value;
			return true;
		}

		return true;
	}

	// DEBUG, se puede borrar
	public function show()
	{
		print_r($this->vars);
	}

	private function build($slug)
	{
		// Check if directory exists for the slug
		/*$path = glob($this->path.$slug, GLOB_ONLYDIR);
		if(empty($path))
			return false;
		

		// Get the first element from the directories array
		//$path = $path[0];
		*/

		$path = $this->path.$slug;
		if(!is_dir($path))
			return false;

		// Path
		$this->setField('path', $path);

		// Slug
		$this->setField('slug', $slug);

		// Check if file exists
		$file = $path.'/index.txt';
		if(!file_exists($file))
			return false;

		$tmp = 0;
		$lines = file($file);
		foreach($lines as $lineNumber=>$line)
		{
			$parts = array_map('trim', explode(':', $line, 2));

			// Lowercase variable
			$parts[0] = helperText::lowercase($parts[0]);

			if($parts[0]==='content')
			{
				$tmp = $lineNumber;
				break;
			}

			if( !empty($parts[0]) && !empty($parts[1]) )
				$this->vars[$parts[0]] = $parts[1];
		}
		
		// Content
		if($tmp!=0)
		{
			$tmp++; // Next line after Content:
			$output = array_slice($lines, $tmp); // Lines after Content
			$this->vars['content'] = implode($output);
		}

		// 

	}

}

?>
