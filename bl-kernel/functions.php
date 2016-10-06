<?php defined('BLUDIT') or die('Bludit CMS.');

// POST FUNCTIONS
// ----------------------------------------------------------------------------

function reIndexTagsPosts()
{
	global $dbPosts;
	global $dbTags;

	// Remove unpublished.
	$dbPosts->removeUnpublished();

	// Regenerate the tags index for posts.
	$dbTags->reindexPosts( $dbPosts->db );

	// Restore the database, before remove the unpublished.
	$dbPosts->restoreDB();

	return true;
}

function buildPost($key)
{
	global $dbPosts;
	global $dbUsers;
	global $Parsedown;
	global $Site;

	// Post object, content from FILE.
	$Post = new Post($key);
	if( !$Post->isValid() ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the post from file with key: '.$key);
		return false;
	}

	// Post database, content from DATABASE JSON.
	$db = $dbPosts->getPostDB($key);
	if( !$db ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the post from database with key: '.$key);
		return false;
	}

	// Foreach field from DATABASE.
	foreach($db as $field=>$value) {
		$Post->setField($field, $value);
	}

	// Content in raw format
	$contentRaw = $Post->content();
	$Post->setField('contentRaw', $contentRaw, true);

	// Parse the content
	$content = Text::pre2htmlentities($contentRaw); // Parse pre code with htmlentities
	$content = $Parsedown->text($content); // Parse Markdown.
	$content = Text::imgRel2Abs($content, HTML_PATH_UPLOADS); // Parse img src relative to absolute.
	$Post->setField('content', $content, true);

	// Pagebrake
	$explode = explode(PAGE_BREAK, $content);
	$Post->setField('breakContent', $explode[0], true);
	$Post->setField('readMore', !empty($explode[1]), true);

	// Date format
	$postDate = $Post->date();
	$Post->setField('dateRaw', $postDate, true);

	$postDateFormated = $Post->dateRaw( $Site->dateFormat() );
	$Post->setField('date', $postDateFormated, true);

	// User object
	$username = $Post->username();
	$Post->setField('user', $dbUsers->getUser($username));

	return $Post;
}

function buildPostsForPage($pageNumber=0, $amount=POSTS_PER_PAGE_ADMIN, $removeUnpublished=true, $tagKey=false)
{
	global $dbPosts;
	global $dbTags;
	global $Url;

	$posts = array();

	if($tagKey) {
		// Get the keys list from tags database, this database is optimized for this case.
		$list = $dbTags->getList($pageNumber, $amount, $tagKey);
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

function buildPage($key)
{
	global $dbPages;
	global $dbUsers;
	global $Parsedown;
	global $Site;

	// Page object, content from FILE.
	$Page = new Page($key);
	if( !$Page->isValid() ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the page from file with key: '.$key);
		return false;
	}

	// Page database, content from DATABASE JSON.
	$db = $dbPages->getPageDB($key);
	if( !$db ) {
		Log::set(__METHOD__.LOG_SEP.'Error occurred when trying build the page from database with key: '.$key);
		return false;
	}

	// Foreach field from DATABASE.
	foreach($db as $field=>$value) {
		$Page->setField($field, $value);
	}

	// Content in raw format
	$contentRaw = $Page->content();
	$Page->setField('contentRaw', $Page->content(), true);

	// Parse markdown content.
	$content = Text::pre2htmlentities($contentRaw); // Parse pre code with htmlentities
	$content = $Parsedown->text($content); // Parse Markdown.
	$content = Text::imgRel2Abs($content, HTML_PATH_UPLOADS); // Parse img src relative to absolute.
	$Page->setField('content', $content, true);

	// Pagebrake
	$explode = explode(PAGE_BREAK, $content);
	$Page->setField('breakContent', $explode[0], true);
	$Page->setField('readMore', !empty($explode[1]), true);

	// Date format
	$pageDate = $Page->date();
	$Page->setField('dateRaw', $pageDate, true);

	$pageDateFormated = $Page->dateRaw( $Site->dateFormat() );
	$Page->setField('date', $pageDateFormated, true);

	// User object
	$username = $Page->username();
	$Page->setField('user', $dbUsers->getUser($username));

	return $Page;
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