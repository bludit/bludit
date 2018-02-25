<?php defined('BLUDIT') or die('Bludit CMS.');

class dbTags extends dbList
{
	function __construct()
	{
		parent::__construct(DB_TAGS);
	}

	function countPagesByTag($tagKey)
	{
		return $this->countItems($tagKey);
	}

	public function reindex()
	{
		global $dbPages;

		// Get a database with published pages
		$db = $dbPages->getPublishedDB(false);

		$tagsIndex = array();

		foreach($db as $pageKey=>$pageFields) {
			$tags = $pageFields['tags'];
			foreach($tags as $tagKey=>$tagName) {
				if( isset($tagsIndex[$tagKey]) ) {
					array_push($tagsIndex[$tagKey]['list'], $pageKey);
				}
				else {
					$tagsIndex[$tagKey]['name'] = $tagName;
					$tagsIndex[$tagKey]['list'] = array($pageKey);
				}
			}
		}

		$this->db = $tagsIndex;
		return $this->save();
	}

}