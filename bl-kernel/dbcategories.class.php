<?php defined('BLUDIT') or die('Bludit CMS.');

class dbCategories extends dbList
{
	function __construct()
	{
		parent::__construct(DB_CATEGORIES);
	}

	function countPagesByCategory($key)
	{
		return $this->countItems($key);
	}

	public function reindex()
	{
		global $dbPages;

		// Foreach category
		foreach( $this->db as $key=>$value ) {
			$this->db[$key]['list'] = array();
		}

		// Foreach post in the database
		$db = $dbPages->getDB();
		foreach($db as $pageKey=>$pageFields) {
			if( !empty($pageFields['category']) ) {
				$categoryKey = $pageFields['category'];
				if( isset($this->db[$categoryKey]['list']) ) {
					array_push($this->db[$categoryKey]['list'], $pageKey);
				}
			}
		}

		return $this->save();
	}
}