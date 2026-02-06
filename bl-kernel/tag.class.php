<?php defined('BLUDIT') or die('Bludit CMS.');

class Tag {

	protected $vars;

	function __construct($key)
	{
		global $tags;
		if (isset($tags->db[$key])) {
			$this->vars['name'] 		= $tags->db[$key]['name'];
			$this->vars['key'] 		= $key;
			$this->vars['permalink'] 	= DOMAIN_TAGS . $key;
			$this->vars['list'] 		= $tags->db[$key]['list'];
		} else {
			$errorMessage = 'Tag not found in database by key ['.$key.']';
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

	// Returns an array with the pages keys linked to the tag
	public function pages()
	{
		return $this->getValue('list');
	}

	// Returns an array in json format with all the data of the tag
	public function json($returnsArray=false)
	{
		$tmp['key'] 		= $this->key();
		$tmp['name'] 		= $this->name();
		$tmp['permalink'] 	= $this->permalink();
		$tmp['pages'] 		= $this->pages();

		if ($returnsArray) {
			return $tmp;
		}

		return json_encode($tmp);
	}
}