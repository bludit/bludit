<?php defined('BLUDIT') or die('Bludit CMS.');

function buildPage($key)
{
	global $dbPages;
	global $dbUsers;
	global $dbCategories;
	global $Parsedown;
	global $Site;

	// Page object, content from index.txt file
	$page = new Page($key);
	if( !$page->isValid() ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the page from file with key: '.$key);
		return false;
	}

	// Get the database from dbPages
	$db = $dbPages->getPageDB($key);
	if( !$db ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the page from database with key: '.$key);
		return false;
	}

	// Foreach field from database set on the object
	foreach($db as $field=>$value) {
		$page->setField($field, $value);
	}

	// Parse Markdown
	$contentRaw = $page->contentRaw();
	$content = Text::pre2htmlentities($contentRaw); // Parse pre code with htmlentities
	$content = $Parsedown->text($content); // Parse Markdown
	$content = Text::imgRel2Abs($content, HTML_PATH_UPLOADS); // Parse img src relative to absolute.
	$page->setField('content', $content, true);

	// Pagebrake
	$explode = explode(PAGE_BREAK, $content);
	$page->setField('contentBreak', $explode[0], true);
	$page->setField('readMore', !empty($explode[1]), true);

	// Date format
	$pageDate = $page->date();
	$page->setField('dateRaw', $pageDate, true);

	$pageDateFormated = $page->dateRaw( $Site->dateFormat() );
	$page->setField('date', $pageDateFormated, true);

	// Generate and set the User object
	$username = $page->username();
	$page->setField('user', $dbUsers->getUser($username));

	// Category
	$categoryKey = $page->categoryKey();
	$page->setField('categoryMap', $dbCategories->getMap($categoryKey));

	return $page;
}

function reindexCategories()
{
	global $dbCategories;
	return $dbCategories->reindex();
}

function reindexTags()
{
	global $dbTags;
	return $dbTags->reindex();
}

function buildPagesForAdmin()
{
	return buildPagesFor('admin');
}

function buildPagesForHome()
{
	return buildPagesFor('home');
}

function buildPagesByCategory()
{
	global $Url;

	$categoryKey = $Url->slug();
	return buildPagesFor('category', $categoryKey, false);
}

function buildPagesByTag()
{
	global $Url;

	$tagKey = $Url->slug();
	return buildPagesFor('tag', false, $tagKey);
}

function buildPagesFor($for, $categoryKey=false, $tagKey=false)
{
	global $dbPages;
	global $dbCategories;
	global $dbTags;
	global $Site;
	global $Url;
	global $pagesByKey;
	global $pages;

	// Get the page number from URL
	$pageNumber = $Url->pageNumber();

	if($for=='admin') {
		$onlyPublished = false;
		$amountOfItems = ITEMS_PER_PAGE_ADMIN;
		$list = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);
	}
	elseif($for=='home') {
		$onlyPublished = true;
		$amountOfItems = $Site->itemsPerPage();
		$list = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);
	}
	elseif($for=='category') {
		$amountOfItems = $Site->itemsPerPage();
		$list = $dbCategories->getList($categoryKey, $pageNumber, $amountOfItems);
	}
	elseif($for=='tag') {
		$amountOfItems = $Site->itemsPerPage();
		$list = $dbTags->getList($tagKey, $pageNumber, $amountOfItems);
	}

	// There are not items for the page number then set the page notfound
	if( empty($list) && $pageNumber>1 ) {
		$Url->setNotFound(true);
	}

	$pages = array(); // global variable
	$pagesByKey = array(); // global variable
	foreach($list as $pageKey=>$fields) {
		$page = buildPage($pageKey);
		if($page!==false) {
			// $pagesByKey
			$pagesByKey[$pageKey] = $page;
			// $pages
			array_push($pages, $page);
		}
	}
	return $pages;
}

// Returns TRUE if the plugin is enabled, FALSE otherwise
function pluginEnabled($pluginName) {
	global $plugins;

	$pluginClass = 'plugin'.Text::firstCharUp($pluginName);
	if( isset($plugins['all'][$pluginClass]) ) {
		return $plugins['all'][$pluginClass]->installed();
	}

	return false;
}

function printDebug($array) {
	echo '<pre>';
	var_dump($array);
	echo '</pre>';
}

function createNewPage($args) {
	global $dbPages;
	global $Syslog;

	$key = $dbPages->add($args);
	if($key) {
		// Call the plugins after page created
		Theme::plugins('afterPageCreate');

		// Re-index categories
		reindexCategories();

		// Re-index tags
		reindextags();

		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'new-page-created',
			'notes'=>$args['title']
		));

		return $key;
	}

	Log::set('Function createNewPage()'.LOG_SEP.'Error occurred when trying to create the page');
	Log::set('Function createNewPage()'.LOG_SEP.'Cleaning database...');
	$dbPages->delete($key);

	return false;
}

function editPage($args) {
	global $dbPages;
	global $Syslog;

	if(!isset($args['parent'])) {
		$args['parent'] = NO_PARENT_CHAR;
	}

	$key = $dbPages->edit($args);
	if($key) {
		// Call the plugins after page modified
		Theme::plugins('afterPageModify');

		// Re-index categories
		reindexCategories();

		// Re-index tags
		reindextags();

		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'page-edited',
			'notes'=>$args['title']
		));

		return $key;
	}

	return false;
}

function deletePage($key) {
	global $dbPages;
	global $Syslog;

	if( $dbPages->delete($key) ) {
		// Call the plugins after page deleted
		Theme::plugins('afterPageDelete');

		// Re-index categories
		reindexCategories();

		// Re-index tags
		reindextags();

		// Add to syslog
		$Syslog->add(array(
			'dictionaryKey'=>'page-deleted',
			'notes'=>$key
		));

		return true;
	}

	return false;
}
