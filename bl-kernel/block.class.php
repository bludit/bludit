<?php defined('BLUDIT') or die('Bludit CMS.');

class Block {

	private $vars;

	function __construct($key)
	{
		global $blocks;
		if (isset($blocks->db[$key])) {
			$this->vars['title'] 		= $blocks->db[$key]['title'];
			$this->vars['value'] 		= $blocks->db[$key]['value'];
			$this->vars['description'] 	= $blocks->db[$key]['description'];
			$this->vars['key'] 		= $key;
		} else {
			$errorMessage = 'Block not found in database by key ['.$key.']';
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

	public function title()
	{
		return $this->getValue('title');
	}

	public function value()
	{
		return $this->getValue('value');
	}

	public function description()
	{
		return $this->getValue('description');
	}

	public function key()
	{
		return $this->getValue('key');
	}
}