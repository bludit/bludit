<?php defined('BLUDIT') or die('Bludit CMS.');

class Tag {

	private $vars;

	function __construct($key)
	{
		global $dbTags;
		if (isset($dbTags->db[$key])) {
			$this->vars['name'] 		= $dbTags->db[$key]['name'];
			$this->vars['template'] 	= $dbTags->db[$key]['template'];
			$this->vars['description'] 	= $dbTags->db[$key]['description'];
			$this->vars['key'] 		= $key;
			$this->vars['permalink'] 	= DOMAIN_TAGS . $key;
			$this->vars['list'] 		= $dbTags->db[$key]['list'];
		} else {
			$errorMessage = 'Category not found in database by key ['.$key.']';
			Log::set(__METHOD__.LOG_SEP.$errorMessage);
			throw new Exception($errorMessage);
		}
	}

	public function getValue($field)
	{
		if (isset($this->vars[$field])) {
			return $this->vars[$field];
		}
		return false;
	}

	public function key()
	{
		return $this->getValue('key');
	}

	public function name()
	{
		return $this->getValue('name');
	}

	public function permalink()
	{
		return $this->getValue('permalink');
	}

	public function template()
	{
		return $this->getValue('template');
	}

	public function description()
	{
		return $this->getValue('description');
	}

	// Returns an array with the keys of pages linked to the tag
	public function pages()
	{
		return $this->getValue('list');
	}
}