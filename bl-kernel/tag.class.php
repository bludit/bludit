<?php defined('BLUDIT') or die('Bludit CMS.');

class Tag {

	private $vars;

	function __construct($key)
	{
		global $dbTags;

		if (isset($dbTags->db[$key])) {
			$this->vars['name'] 		= $dbTags->db[$key]['name'];
			$this->vars['key'] 		= $key;
			$this->vars['permalink'] 	= DOMAIN_TAGS . $key;
			$this->vars['list'] 		= $dbTags->db[$key]['list'];
		}
		else {
			$this->vars = false;
		}
	}

	// Returns TRUE if the tag is valid/exists, FALSE otherwise
	public function isValid()
	{
		return $this->vars!==false;
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

	// Returns an array with the keys of pages linked to the tag
	public function pages()
	{
		return $this->getValue('list');
	}
}