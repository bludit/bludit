<?php defined('BLUDIT') or die('Bludit CMS.');

class Category {

	private $vars;

	function __construct($key)
	{
		global $dbCategories;

		if (isset($dbCategories->db[$key])) {
			$this->vars['name'] 		= $dbCategories->db[$key]['name'];
			$this->vars['template'] 	= $dbCategories->db[$key]['template'];
			$this->vars['key'] 		= $key;
			$this->vars['permalink'] 	= DOMAIN_CATEGORIES . $key;
			$this->vars['list'] 		= $dbCategories->db[$key]['list'];
		}
		else {
			$this->vars = false;
		}
	}

	// Returns TRUE if the category is valid/exists, FALSE otherwise
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

	public function template()
	{
		return $this->getValue('template');
	}

	// Returns an array with the keys of pages linked to the category
	public function pages()
	{
		return $this->getValue('list');
	}
}
