<?php defined('BLUDIT') or die('Bludit CMS.');

/*
Database structure

{
	"videos": {
		"name": "Videos",
		"list": [ "my-page", "second-page" ]
	},
	"pets": {
		"name": "Pets",
		"list": [ "cats-and-dogs" ]
	}
}
*/

class dbList extends dbJSON
{
	public $db = array();

	function __construct($file)
	{
		parent::__construct($file);
	}

	// Returns an array with a list of key of pages, FALSE if out of range
	public function getList($key, $pageNumber, $amountOfItems)
	{
		if (empty($key)) {
			return false;
		}

		if (!isset($this->db[$key])) {
			Log::set(__METHOD__.LOG_SEP.'Error key does not exist '.$key);
			return false;
		}

		$list = $this->db[$key]['list'];

		if ($amountOfItems==-1) {
			// Invert keys to values, is necesary returns as key the key pages
			//$list = array_flip($list);
			return $list;
		}

		// The first page number is 1, so the real is 0
		$realPageNumber = $pageNumber - 1;

		$total = count($list);
		$init = (int) $amountOfItems * $realPageNumber;
		$end  = (int) min( ($init + $amountOfItems - 1), $total );
		$outrange = $init<0 ? true : $init>$end;

		if($outrange) {
			Log::set(__METHOD__.LOG_SEP.'Error out of range');
			return false;
		}

		//$list = array_flip($list);
		return array_slice($list, $init, $amountOfItems, true);
	}

	public function generateKey($name)
	{
		$key = Text::cleanUrl($name);
		if (empty($key)) {
			return false;
		}
		return $key;
	}

	public function add($name)
	{
		$key = $this->generateKey($name);
		if ($key===false) {
			Log::set(__METHOD__.LOG_SEP.'Error when try to generate the key');
			return false;
		}

		if (isset($this->db[$key])) {
			Log::set(__METHOD__.LOG_SEP.'Error key already exist: '.$key);
			return false;
		}

		$this->db[$key]['name'] = Sanitize::html($name);
		$this->db[$key]['list'] = array();

		$this->sortAlphanumeric();
		$this->save();

		return $key;
	}

	public function remove($key)
	{
		if( !isset($this->db[$key]) ) {
			Log::set(__METHOD__.LOG_SEP.'The key does not exist, key: '.$key);
			return false;
		}

		unset($this->db[$key]);
		return $this->save();
	}

	public function edit($oldKey, $newName)
	{
		$newKey = $this->generateKey($newName);

		$this->db[$newKey]['name'] = Sanitize::html($newName);
		$this->db[$newKey]['list'] = $this->db[$oldKey]['list'];

		// Remove the old key
		if( $oldKey != $newKey ) {
			unset( $this->db[$oldKey] );
		}

		$this->sortAlphanumeric();
		$this->save();
		return $newKey;
	}

	// Sort the categories by "Natural order"
	private function sortAlphanumeric()
	{
		// Sort key alphanumeric strings, a01, a10, b10, c02
		return ksort($this->db);
	}

	// Returns the name associated to the key, FALSE if the key doesn't exist
	public function getName($key)
	{
		if( isset($this->db[$key]) ) {
			return $this->db[$key]['name'];
		}

		return false;
	}

	// Returns an array with key=>name of the list
	public function getKeyNameArray()
	{
		$tmp = array();
		foreach($this->db as $key=>$fields) {
			$tmp[$key] = $fields['name'];
		}

		return $tmp;
	}

	// Returns the amount of items for some key
	public function countItems($key)
	{
		if( isset($this->db[$key]) ) {
			return count($this->db[$key]['list']);
		}

		return 0;
	}

	public function exists($key)
	{
		return isset( $this->db[$key] );
	}

	// Returns an array with a portion of the database filtered by key
	// Returns array( 'name'=>'', 'list'=>array() )
	public function getMap($key)
	{
		if( isset($this->db[$key]) ) {
			return $this->db[$key];
		}

		return false;
	}

}