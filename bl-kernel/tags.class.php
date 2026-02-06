<?php defined('BLUDIT') or die('Bludit CMS.');

class Tags extends dbList {

	function __construct()
	{
		parent::__construct(DB_TAGS);
	}

	function numberOfPages($key)
	{
		return $this->countItems($key);
	}

	public function reindex()
	{
		global $pages;
		$db = $pages->getDB($onlyKeys=false);
		$tagsIndex = array();
		foreach ($db as $pageKey=>$pageFields) {
			if (in_array($pageFields['type'], $GLOBALS['DB_TAGS_TYPES'])) {
				$tags = $pageFields['tags'];
				foreach ($tags as $tagKey=>$tagName) {
					if (isset($tagsIndex[$tagKey])) {
						array_push($tagsIndex[$tagKey]['list'], $pageKey);
					} else {
						$tagsIndex[$tagKey]['name'] = $tagName;
						$tagsIndex[$tagKey]['list'] = array($pageKey);
					}
				}
			}
		}

		$this->db = $tagsIndex;
		$this->sortAlphanumeric();
		return $this->save();
	}

}