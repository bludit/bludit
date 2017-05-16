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

	private function getList($key, $amountOfItems, $pageNumber)
	{
		if( !isset($this->db[$key]) ) {
			Log::set(__METHOD__.LOG_SEP.'Error key does not exist '.$key);
			return array();
		}

		$list = $this->db[$key]['list'];

		$total = count($list);
		$init = (int) $amountOfItems * $pageNumber;
		$end  = (int) min( ($init + $amountOfItems - 1), $total );
		$outrange = $init<0 ? true : $init>$end;

		if($outrange) {
			Log::set(__METHOD__.LOG_SEP.'Error out of range');
			return array();
		}

		$list = array_flip($list);
		return array_slice($list, $init, $amountOfItems, true);
	}

	public function generateKey($name)
	{
		return Text::cleanUrl($name);
	}

	public function add($name)
	{
		$key = $this->generateKey($name);
		if( isset($this->db[$key]) ) {
			Log::set(__METHOD__.LOG_SEP.'Error key already exist: '.$key);
			return false;
		}

		$this->db[$key]['name'] = $name;
		$this->db[$key]['list'] = array();
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

		$this->db[$newKey]['name'] = $newName;
		$this->db[$newKey]['list'] = $this->db[$oldKey]['list'];

		// Remove the old category
		if( $oldKey != $newKey ) {
			unset( $this->db[$oldKey] );
		}

		$this->save();
		return $newKey;
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
	public function getKeyNameArray($sortAlphanumeric=true)
	{
		$tmp = array();
		foreach($this->db as $key=>$fields) {
			$tmp[$key] = $fields['name'];
		}

		// Sort alphanumeric strings, a01, a10, a11, a20
		if($sortAlphanumeric) {
			natcasesort($tmp);
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