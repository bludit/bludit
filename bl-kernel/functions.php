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

function buildPagesForAdmin($pageNumber)
{
	return buildPagesFor('admin', $pageNumber);
}

function buildPagesForHome($pageNumber)
{
	return buildPagesFor('home', $pageNumber);
}

function buildPagesFor($for, $pageNumber)
{
	global $dbPages;
	global $Site;

	if($for=='admin') {
		$list = $dbPages->getList($pageNumber, ITEMS_PER_PAGE_ADMIN, false);
	}
	elseif($for=='home') {
		$list = $dbPages->getList($pageNumber, $Site->postsPerPage(), true);
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

// ---- OLD


function buildPostsForPage($pageNumber=0, $amount=POSTS_PER_PAGE_ADMIN, $removeUnpublished=true, $key=false, $type='tag')
{
	global $dbPosts;
	global $dbTags;
	global $dbCategories;
	global $Url;

	$posts = array();

	if( $type=='tag' && $key ) {
		// Get the keys list from tags database, this database is optimized for this case.
		$list = $dbTags->getList($pageNumber, $amount, $key);
	}
	elseif( $type=='category' && $key ) {
		$list = $dbCategories->getListOfPosts($pageNumber, $amount, $key);
	}
	else {
		// Get the keys list from posts database.
		$list = $dbPosts->getList($pageNumber, $amount, $removeUnpublished);
	}

	// There are not posts for the page number then set the page notfound
	if(empty($list) && $pageNumber>0) {
		$Url->setNotFound(true);
	}

	// Foreach post key, build the post.
	foreach($list as $postKey=>$values)
	{
		$Post = buildPost($postKey);
		if($Post!==false) {
			array_push($posts, $Post);
		}
	}

	return $posts;
}

// PAGE FUNCTIONS
// ----------------------------------------------------------------------------

function sortPages($a, $b)
{
	if ($a['position'] == $b['position']) {
	    return 0;
	}

	return ($a['position'] < $b['position']) ? -1 : 1;
}

function buildAllPages()
{
	global $pagesParents;
	global $pagesParentsPublished;
	global $pagesPublished;
	global $dbPages;
	global $parents;

	// Get the page list
	$list = $dbPages->getDB();

	// Clean pages array.
	$pages = array();

	// Remove the error page
	unset($list['error']);

	// Sorte pages
	uasort($list, 'sortPages');

	foreach($list as $key=>$db)
	{
		$Page = buildPage($key);

		if($Page!==false)
		{
			// Filter pages, with and without parent

			// If the page doesn't have a father, it's a parent page :P
			if( $Page->parentKey()===false ) {
				// Add the parent key in the dbPages
				$dbPages->addParentKey($Page->key());

				// Add the page as a parent page in the array
				$pagesParents[NO_PARENT_CHAR][$Page->key()] = $Page;

				// If the page is published
				if($Page->published()) {
					$pagesParentsPublished[NO_PARENT_CHAR][$Page->key()] = $Page;
				}
			}
			else {
				$pagesParents[$Page->parentKey()][$Page->key()] = $Page;

				// If the page is published
				if($Page->published()) {
					$pagesParentsPublished[$Page->parentKey()][$Page->key()] = $Page;
				}
			}

			// All pages in one array
			$pages[$Page->key()] = $Page;

			// If the page is published
			if($Page->published()) {
				$pagesPublished[$Page->parentKey()][$Page->key()] = $Page;
			}
		}
	}

	if( isset($pagesParentsPublished[NO_PARENT_CHAR]) ) {
		$parents = $pagesParentsPublished[NO_PARENT_CHAR];
	}

	return $pages;
}