<?php defined('BLUDIT') or die('Bludit CMS.');

class Blocks extends dbJSON
{
	// Fields allowed for a row in the database
	protected $dbFields = array(
		'title'=>'',
		'value'=>'',
		'description'=>''
	);

	function __construct()
	{
		parent::__construct(DB_BLOCKS);
	}

	// Get a particular Block-Object by his key
	public function get($key)
	{
		return new Block($key);
	}

	// Get an array with all the Block-Object
	public function getAll()
	{
		$all = array();
		foreach ($this->db as $key=>$fields) {
			$all[$key] = new Block($key);
		}
		return $all;
	}

	// Add a row to the database
	public function add($args)
	{
		$key = $this->generateKey($args['key']);
		if (Text::isEmpty($key)) {
			Log::set(__METHOD__.LOG_SEP.'Invalid key for the Block.', LOG_TYPE_ERROR);
			return false;
		}

		$row = array();
		foreach ($this->dbFields as $field=>$defaultValue) {
			if (isset($args[$field])) {
				// Sanitize if will be stored on database
				$value = Sanitize::html($args[$field]);
				settype($value, gettype($defaultValue));
				$row[$field] = $value;
			}
		}
		// Insert the row in the database
		$this->db[$key] = $row;
		// Save the database
		return $this->save();
	}

	// Delete a row from the database
	public function delete($key)
	{
		if (!$this->exists($key)) {
			Log::set(__METHOD__.LOG_SEP.'The Block does not exist. Key: '.$key, LOG_TYPE_ERROR);
		}

		// Remove from database
		unset($this->db[$key]);
		// Save the database
		return $this->save();
	}

	// Check if a row exists
	public function exists($key)
	{
		return isset ($this->db[$key]);
	}

	private function generateKey($text)
	{
		return Text::cleanUrl($text);
	}

}
