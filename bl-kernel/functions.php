<?php defined('BLUDIT') or die('Bludit CMS.');

function buildPage($key)
{
	global $dbPages;
	global $dbUsers;
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

	return $page;
}

function reindexCategories()
{
	global $dbPages;
	global $dbCategories;

	// Get a database with published pages
	$db = $dbPages->getPublishedDB();

	// Regenerate the tags
	$dbCategories->reindex($db);

	return true;
}

function reindexTags()
{
	global $dbPages;
	global $dbCategories;

	// Get a database with published pages
	$db = $dbPages->getPublishedDB();

	// Regenerate the tags
	$dbTags->reindex($db);

	return true;
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
	global $Site;

	// Get the page number from URL
	$pageNumber = $Url->pageNumber();

	if($for=='admin') {
		$onlyPublished = false;
		$amountOfItems = ITEMS_PER_PAGE_ADMIN;
		$list = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);
	}
	elseif($for=='home') {
		$onlyPublished = true;
		$amountOfItems = $Site->postsPerPage();
		$list = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);
	}
	elseif($for=='category') {
		$amountOfItems = $Site->postsPerPage();
		$list = $dbCategories->getList($categoryKey, $pageNumber, $amountOfItems);
	}
	elseif($for=='tag') {
		$amountOfItems = $Site->postsPerPage();
		$list = $dbTags->getList($tagKey, $pageNumber, $amountOfItems);
	}

	// There are not items for the page number then set the page notfound
	if( empty($list) && $pageNumber>0 ) {
		$Url->setNotFound(true);
	}

	$pages = array();
	foreach($list as $pageKey=>$fields) {
		$page = buildPage($pageKey);
		if($page!==false) {
			array_push($pages, $page);
		}
	}
	return $pages;
}
