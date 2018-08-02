<?php defined('BLUDIT') or die('Bludit CMS.');

class Tag {

	private $vars;

	function __construct($key)
	{
		global $tags;
		if (isset($tags->db[$key])) {
			$this->vars['name'] 		= $tags->db[$key]['name'];
			$this->vars['template'] 	= $tags->db[$key]['template'];
			$this->vars['description'] 	= $tags->db[$key]['description'];
			$this->vars['key'] 		= $key;
			$this->vars['permalink'] 	= DOMAIN_TAGS . $key;
			$this->vars['list'] 		= $tags->db[$key]['list'];
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