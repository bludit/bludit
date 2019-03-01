<?php defined('BLUDIT') or die('Bludit CMS.');

class Categories extends dbList {

	function __construct()
	{
		parent::__construct(DB_CATEGORIES);
	}

	function numberOfPages($key)
	{
		return $this->countItems($key);
	}

	public function reindex()
	{
		global $pages;

		// Foreach category
		foreach ($this->db as $key=>$value) {
			$this->db[$key]['list'] = array();
		}

		// Get pages database
		$db = $pages->getDB(false);
		foreach ($db as $pageKey=>$pageFields) {
			if (!empty($pageFields['category'])) {
				$categoryKey = $pageFields['category'];
				if (isset($this->db[$categoryKey]['list'])) {
					if (
						($db[$pageKey]['type']=='published') ||
						($db[$pageKey]['type']=='sticky') ||
						($db[$pageKey]['type']=='static')
					) {
						array_push($this->db[$categoryKey]['list'], $pageKey);
					}
				}
			}
		}

		return $this->save();
	}
}