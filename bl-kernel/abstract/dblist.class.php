<?php defined('BLUDIT') or die('Bludit CMS.');

/*
Database structure

{
	"videos": {
		"name": "Videos",
		"template: "",
		"description: "",
		"list": [ "my-page", "second-page" ]
	},
	"pets": {
		"name": "Pets",
		"template: "",
		"description: "",
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

	public function keys()
	{
		return array_keys($this->db);
	}

	// Returns the list of keys filter by pageNumber
	// $pageNumber start in 1
	public function getList($key, $pageNumber, $numberOfItems)
	{
		if (!isset($this->db[$key])) {
			Log::set(__METHOD__.LOG_SEP.'Error key does not exist '.$key);
			return false;
		}

		// List of keys
		$list = $this->db[$key]['list'];

		// Returns all the items from the list
		if ($numberOfItems==-1) {
			return $list;
		}

		// The first page number is 1, so the real is 0
		$realPageNumber = $pageNumber - 1;
		$chunks = array_chunk($list, $numberOfItems);
		if (isset($chunks[$realPageNumber])) {
			return $chunks[$realPageNumber];
		}

		// Out of index,returns FALSE
		return false;
	}

	public function generateKey($name)
	{
		global $L;

		$key = Text::cleanUrl($name);
		if (Text::isEmpty($key)) {
			$key = $L->g('empty');
		}
		while (isset($this->db[$key])) {
			$key++;
		}
		return $key;
	}

	// Add a new item to the dblist
	// $args => 'name', 'template', 'description', list'
	public function add($args)
	{
		$key = $this->generateKey($args['name']);

		$this->db[$key]['name'] 	= Sanitize::removeTags($args['name']);
		$this->db[$key]['template'] 	= isset($args['template'])?Sanitize::removeTags($args['template']):'';
		$this->db[$key]['description'] 	= isset($args['description'])?Sanitize::removeTags($args['description']):'';
		$this->db[$key]['list'] 	= isset($args['list'])?$args['list']:array();

		$this->sortAlphanumeric();
		$this->save();
		return $key;
	}

	public function remove($key)
	{
		if (!isset($this->db[$key])) {
			Log::set(__METHOD__.LOG_SEP.'The key does not exist, key: '.$key);
			return false;
		}

		unset($this->db[$key]);
		return $this->save();
	}

	// Edit an item to the dblist
	// $args => 'name', 'oldkey', 'newKey', 'template', 'description'
	public function edit($args)
	{
		if ( isset($this->db[$args['newKey']]) && ($args['newKey']!==$args['oldKey']) ) {
			Log::set(__METHOD__.LOG_SEP.'The new key already exists. Key: '.$args['newKey'], LOG_TYPE_WARN);
			return false;
		}

		$this->db[$args['newKey']]['name'] 	= Sanitize::removeTags($args['name']);
		$this->db[$args['newKey']]['template'] 	= isset($args['template'])?Sanitize::removeTags($args['template']):'';
		$this->db[$args['newKey']]['description'] = isset($args['description'])?Sanitize::removeTags($args['description']):'';
		$this->db[$args['newKey']]['list'] 	= $this->db[$args['oldKey']]['list'];

		// Remove the old category
		if ($args['oldKey'] !== $args['newKey']) {
			unset( $this->db[$args['oldKey']] );
		}

		$this->sortAlphanumeric();
		$this->save();
		return $args['newKey'];
	}

	// Sort the categories by "Natural order"
	public function sortAlphanumeric()
	{
		// Sort key alphanumeric strings, a01, a10, b10, c02
		return ksort($this->db);
	}

	// Returns the name associated to the key, FALSE if the key doesn't exist
	public function getName($key)
	{
		if (isset($this->db[$key])) {
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

	// Returns the number of items in the list
	public function countItems($key)
	{
		if (isset($this->db[$key])) {
			return count($this->db[$key]['list']);
		}
		return 0;
	}

	public function exists($key)
	{
		return isset( $this->db[$key] );
	}

	public function existsName($name)
	{
		foreach ($this->db as $key=>$fields) {
			if ($name==$fields['name']) {
				return true;
			}
		}
		return false;
	}

	// Returns an array with a portion of the database filtered by key
	// Returns array( 'key'=>'', 'name'=>'', 'template'=>'', 'description'=>'', list'=>array() )
	public function getMap($key)
	{
		if (isset($this->db[$key])) {
			$tmp = $this->db[$key];
			$tmp['key'] = $key;
			return $tmp;
		}
		return false;
	}

}